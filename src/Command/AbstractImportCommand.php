<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Command;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\ImporterInterface;
use Camelot\ApiImporter\JobMonitor\Stats;
use Camelot\ApiImporter\JobMonitor\Stopwatch;
use Camelot\ApiImporter\Options\Options;
use Camelot\ApiImporter\Options\OptionsInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractImportCommand extends Command implements SignalableCommandInterface
{
    protected SymfonyStyle $io;
    protected Stopwatch $stopwatch;
    protected Stats $stats;

    public function __construct(protected ImporterInterface $importer)
    {
        $this->stopwatch = new Stopwatch();
        $this->stats = new Stats();

        parent::__construct();
    }

    public function getSubscribedSignals(): array
    {
        return [SIGINT, SIGTERM];
    }

    public function handleSignal(int $signal, false|int $previousExitCode = 0): false|int
    {
        $this->report(ResultCode::WARNING);

        return Command::INVALID;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, $this->getFileArgumentDescription())
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Type of data file (csv or tsv)', $this->getDefaultFileType())
            ->addOption('header-offset', 'o', InputOption::VALUE_REQUIRED, 'Header row in file (zero indexed)', $this->getDefaultHeaderOffset())
            ->addOption('start-row', 'r', InputOption::VALUE_REQUIRED, 'Row number to start from in data file', 0)
            ->addOption('skip-existing', 's', InputOption::VALUE_NONE, 'Skip existing records')
            ->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Number of records to batch', 1000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $options = $this->getImportOptions($input);

        $progress = $this->io->createProgressBar($options->getBatchSize());
        $progress->setFormat("<info>%message%</info>\n %current% [%bar%] %elapsed:6s% %memory:6s%");
        $progress->setMessage('Starting …');

        $this->stopwatch->start();

        $status = ResultCode::SUCCESS;

        try {
            foreach ($this->importer->import($options, $this->stats) as $_) {
                $progress->setMessage('Reading import data … ');
                $progress->advance();

                if ($progress->getProgress() >= $progress->getMaxSteps()) {
                    $progress->setMaxSteps($progress->getMaxSteps() + $options->getBatchSize());
                }
            }
        } catch (\Throwable $e) {
            $status = ResultCode::FAILURE;
            throw $e;
        } finally {
            $this->stopwatch->stop();
            $progress->finish();
            $this->report($status);
        }

        return Command::SUCCESS;
    }

    protected function getImportOptions(InputInterface $input): OptionsInterface
    {
        $headerOffset = $input->getOption('header-offset');

        if (!file_exists($input->getArgument('file'))) {
            throw new \RuntimeException(sprintf('File %s does not exist.', $input->getArgument('file')));
        }

        return Options::create()
            ->withFilePathname($input->getArgument('file'))
            ->withFileType($input->getOption('type'))
            ->withEntityClass($this->getEntityClass())
            ->withStartRow((int) $input->getOption('start-row'))
            ->withHeaderOffset(\is_string($headerOffset) ? (int) $headerOffset : $headerOffset)
            ->withSkipExisting((bool) $input->getOption('skip-existing'))
            ->withBatchSize((int) $input->getOption('batch-size'))
        ;
    }

    protected function getDefaultFileType(): string
    {
        return 'csv';
    }

    protected function getDefaultHeaderOffset(): ?int
    {
        return null;
    }

    abstract protected function getFileArgumentDescription(): string;

    abstract protected function getEntityClass(): string;

    private function report(ResultCode $resultCode): void
    {
        $method = match ($resultCode) {
            ResultCode::FAILURE => 'error',
            default => $resultCode->value,
        };

        $this->io->{$method}([
            sprintf('Import Complete: %s', strtoupper($resultCode->value)),
            sprintf('Runtime:   %s minutes', number_format($this->stopwatch->runtime() / 60, 2)),
            sprintf('Memory:    %s MiB', number_format(memory_get_peak_usage(true) / 1024 / 1024, 2)),
            sprintf(
                "Processed: %s\n - Created: %s\n - Updated: %s\n - Skipped: %s",
                number_format($this->importer->count()),
                number_format($this->stats->getCreated()),
                number_format($this->stats->getUpdated()),
                number_format($this->stats->getSkipped()),
            ),
        ]);
    }
}
