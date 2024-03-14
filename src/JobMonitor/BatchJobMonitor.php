<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\JobMonitor;

use Camelot\ApiImporter\Enum\Outcome;
use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Exception\JobMonitorException;
use Camelot\ApiImporter\Handler\Result;
use Camelot\ApiImporter\Options\OptionsInterface;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

final class BatchJobMonitor implements BatchJobMonitorInterface
{
    /** @var Middleware[] */
    private array $middlewares;
    private bool $started = false;
    private int $max = 1000;
    private Stats $stats;

    public function __construct(
        private EntityManagerInterface $em,
        private ManagerRegistry $managerRegistry,
        private LoggerInterface $logger,
    ) {}

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function start(OptionsInterface $options, Stats $stats): void
    {
        if ($this->started) {
            throw new JobMonitorException('Monitor already started.');
        }

        $this->stats = $stats;
        $dbConfig = $this->em->getConnection()->getConfiguration();
        $this->middlewares = $dbConfig->getMiddlewares();
        $dbConfig->setMiddlewares([]);

        $this->max = $options->getBatchSize();
        $this->started = true;
    }

    /** @return bool True if a flush occurred, false otherwise. */
    public function next(Result $result): bool
    {
        if (!$this->started) {
            throw new JobMonitorException('Monitor NOT started!');
        }

        match ($result->getOutcome()) {
            Outcome::SKIPPED => $this->skip($result),
            Outcome::CREATED => $this->create($result),
            Outcome::UPDATED => $this->update($result),
        };

//        $this->logger->info(sprintf('[%s] %s # %s', strtoupper($result->getOutcome()->value), $result->getEntity()::class, $result->getMessage()));

        $units = $this->em->getUnitOfWork()->size();
        if ($units !== 0 && $units % $this->max === 0) {
            $this->flush();

            return true;
        }

        return false;
    }

    public function stop(ResultCode $resultCode): void
    {
        if (!$this->started) {
            throw new JobMonitorException('Monitor NOT started!');
        }

        try {
            if ($this->em->isOpen()) {
                $this->flush();
            }
        } finally {
            if ($this->middlewares) {
                $dbConfig = $this->em->getConnection()->getConfiguration();
                $dbConfig->setMiddlewares($this->middlewares);
            }

            $this->started = false;
        }
    }

    public function count(): int
    {
        return $this->stats->getCreated() + $this->stats->getUpdated() + $this->stats->getSkipped();
    }

    private function flush(): void
    {
        try {
            $this->em->beginTransaction();
            $this->em->flush();
            $this->em->commit();
            $this->em->clear();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            $this->em->rollback();
            $this->managerRegistry->resetManager();

            throw $e;
        }
    }

    private function create(Result $result): void
    {
        $this->stats->addCreated();
        $this->em->persist($result->getEntity());
    }

    private function update(Result $result): void
    {
        $this->stats->addUpdated();
        $this->em->persist($result->getEntity());
    }

    private function skip(Result $result): void
    {
        $this->stats->addSkipped();

        if ($this->em->contains($result->getEntity())) {
            $this->em->detach($result->getEntity());
        }
    }
}
