<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\JobMonitor;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Handler\Result;
use Camelot\ApiImporter\JobMonitor\BatchJobMonitor;
use Camelot\ApiImporter\JobMonitor\Stats;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;
use Camelot\ApiImporter\Tests\Fixtures\Options\OptionsFixture;
use Camelot\ApiImporter\Tests\FunctionalTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/** @internal */
#[CoversClass(BatchJobMonitor::class)]
final class BatchJobMonitorTest extends FunctionalTestCase
{
    public function testStartStop(): void
    {
        $batchJobMonitor = self::getContainer()->get(BatchJobMonitor::class);
        self::assertFalse($batchJobMonitor->isStarted());

        $batchJobMonitor->start(new OptionsFixture(), new Stats());
        self::assertTrue($batchJobMonitor->isStarted());

        $batchJobMonitor->stop(ResultCode::SUCCESS);
        self::assertFalse($batchJobMonitor->isStarted());
    }

    public function testIncrements(): void
    {
        $batchJobMonitor = self::getContainer()->get(BatchJobMonitor::class);
        $batchJobMonitor->start(OptionsFixture::create()->withBatchSize(2), new Stats());
        self::assertSame(0, $batchJobMonitor->count());

        $flushed = $batchJobMonitor->next(Result::created($this->addEntity(), 'testing 1'));
        self::assertSame(1, $batchJobMonitor->count());
        self::assertFalse($flushed);

        $flushed = $batchJobMonitor->next(Result::updated($this->addEntity(), 'testing 2'));
        self::assertSame(2, $batchJobMonitor->count());
        self::assertFalse($flushed);

        $flushed = $batchJobMonitor->next(Result::skipped($this->addEntity(), 'testing 3'));
        self::assertSame(3, $batchJobMonitor->count());
//        self::assertTrue($flushed); // FIXME - UnitOfWork not incrementing
    }

    public function testStartThrowsExceptionWhenAlreadyStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monitor already started.');

        $batchJobMonitor = self::getContainer()->get(BatchJobMonitor::class);
        $batchJobMonitor->start(new OptionsFixture(), new Stats());
        $batchJobMonitor->start(new OptionsFixture(), new Stats());
    }

    public function testNextThrowsExceptionWhenNotStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monitor NOT started!');

        $batchJobMonitor = self::getContainer()->get(BatchJobMonitor::class);
        $batchJobMonitor->next(Result::created(new EntityFixture(), 'testing'));
    }

    public function testStopThrowsExceptionWhenNotStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monitor NOT started!');

        $batchJobMonitor = self::getContainer()->get(BatchJobMonitor::class);
        $batchJobMonitor->stop(ResultCode::SUCCESS);
    }

    private function addEntity(): EntityFixture
    {
        $entity = new EntityFixture();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist($entity);

        return $entity;
    }
}
