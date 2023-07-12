<?php

declare(strict_types=1);

namespace Camelot\ApiImporter;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Exception\ImportException;
use Camelot\ApiImporter\Exception\JobMonitorException;
use Camelot\ApiImporter\Handler\HandlerInterface;
use Camelot\ApiImporter\JobMonitor\BatchJobMonitorInterface;
use Camelot\ApiImporter\JobMonitor\ImportJobMonitorInterface;
use Camelot\ApiImporter\JobMonitor\Stats;
use Camelot\ApiImporter\Options\OptionsInterface;
use Camelot\ApiImporter\Reader\ReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final class DataImporter implements ImporterInterface
{
    /** @var ReaderInterface[] */
    private iterable $readers;
    /** @var HandlerInterface[] */
    private iterable $handlers;

    public function __construct(
        private ImportJobMonitorInterface $importJobMonitor,
        private BatchJobMonitorInterface $batchJobMonitor,
        #[TaggedIterator('camelot.api_import.readers')]
        iterable $readers,
        #[TaggedIterator('camelot.api_import.handler')]
        iterable $handlers,
    ) {
        $this->readers = $readers;
        $this->handlers = $handlers;
    }

    public function count(): int
    {
        try {
            return $this->importJobMonitor->count();
        } catch (JobMonitorException $e) {
            return 0;
        }
    }

    public function import(OptionsInterface $options, Stats $stats): \Generator
    {
        $this->importJobMonitor->start($options);
        $this->batchJobMonitor->start($options, $stats);

        try {
            yield from $this->runImport($options);
        } catch (\Throwable $e) {
            $this->importJobMonitor->stop(ResultCode::FAILURE);
            throw $e;
        } finally {
            if ($this->importJobMonitor->isStarted()) {
                $this->importJobMonitor->stop(ResultCode::SUCCESS);
            }
            if ($this->batchJobMonitor->isStarted()) {
                $this->batchJobMonitor->stop(ResultCode::SUCCESS);
            }
        }
    }

    private function runImport(OptionsInterface $options): \Generator
    {
        $reader = $this->getReader($options->getFileType());
        $handler = $this->getHandler($options->getEntityClass());

        foreach ($reader->readFile($options->getFilePathname(), $options->getHeaderOffset()) as $index => $line) {
            if ($index < $options->getStartRow()) {
                continue;
            }

            $this->importJobMonitor->next();
            $this->batchJobMonitor->next($handler->handle($line, $options));

            yield $index;
        }
    }

    private function getReader(string $type): ReaderInterface
    {
        foreach ($this->readers as $reader) {
            if ($reader->supports($type)) {
                return $reader;
            }
        }

        throw new ImportException(sprintf('File reader not found for %s', $type));
    }

    private function getHandler(string $entityClass): HandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($entityClass)) {
                return $handler;
            }
        }

        throw new ImportException(sprintf('Import handler not found for %s', $entityClass));
    }
}
