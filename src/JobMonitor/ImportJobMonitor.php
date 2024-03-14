<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\JobMonitor;

use Camelot\ApiImporter\Entity\ImportJob;
use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Exception\JobMonitorException;
use Camelot\ApiImporter\Options\OptionsInterface;
use Camelot\ApiImporter\Repository\ImportJobRepository;

final class ImportJobMonitor implements ImportJobMonitorInterface
{
    private ?Stopwatch $stopwatch = null;
    private ?int $jobId = null;

    public function __construct(private ImportJobRepository $importJobRepository) {}

    public function isStarted(): bool
    {
        return $this->stopwatch?->isStarted() ?? false;
    }

    public function start(OptionsInterface $options): void
    {
        if ($this->stopwatch?->isStarted()) {
            throw new JobMonitorException('Monitor already started.');
        }

        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start();

        $job = new ImportJob();
        $job
            ->setEntityClass($options->getEntityClass())
            ->setExecuted(new \DateTimeImmutable())
        ;
        $this->importJobRepository->save($job, true);

        $this->jobId = $job->getId();
    }

    public function next(): void
    {
        if (!$this->stopwatch?->isStarted()) {
            throw new JobMonitorException('Monitor NOT started!');
        }

        $job = $this->getJob();
        $job->setRecords(1 + $job->getRecords());
        $this->importJobRepository->save($job);
    }

    public function stop(ResultCode $result): void
    {
        if (!$this->stopwatch?->isStarted()) {
            throw new JobMonitorException('Monitor NOT started!');
        }

        $this->stopwatch->stop();

        $job = $this->getJob();
        $job
            ->setResult($result)
            ->setRuntime((int) $this->stopwatch->runtime())
            ->setMemory($this->stopwatch->memory())
        ;

        $this->importJobRepository->save($job, true);
    }

    public function count(): int
    {
        return $this->getJob()->getRecords() ?? 0;
    }

    private function getJob(): ImportJob
    {
        return $this->importJobRepository->find($this->jobId);
    }
}
