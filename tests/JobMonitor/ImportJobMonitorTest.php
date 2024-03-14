<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\JobMonitor;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\JobMonitor\ImportJobMonitor;
use Camelot\ApiImporter\Tests\Fixtures\Options\OptionsFixture;
use Camelot\ApiImporter\Tests\FunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/** @internal */
#[CoversClass(ImportJobMonitor::class)]
final class ImportJobMonitorTest extends FunctionalTestCase
{
    public function testStartStop(): void
    {
        $importJobMonitor = self::getContainer()->get(ImportJobMonitor::class);
        self::assertFalse($importJobMonitor->isStarted());

        $importJobMonitor->start(OptionsFixture::create());
        self::assertTrue($importJobMonitor->isStarted());

        $importJobMonitor->stop(ResultCode::SUCCESS);
        self::assertFalse($importJobMonitor->isStarted());
    }

    public function testIncrements(): void
    {
        $importJobMonitor = self::getContainer()->get(ImportJobMonitor::class);
        $importJobMonitor->start(OptionsFixture::create());
        self::assertSame(0, $importJobMonitor->count());

        $importJobMonitor->next();
        self::assertSame(1, $importJobMonitor->count());

        $importJobMonitor->next();
        self::assertSame(2, $importJobMonitor->count());

        $importJobMonitor->next();
        self::assertSame(3, $importJobMonitor->count());
    }

    public function testStartThrowsExceptionWhenAlreadyStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monitor already started.');

        $importJobMonitor = self::getContainer()->get(ImportJobMonitor::class);
        $importJobMonitor->start(OptionsFixture::create());
        $importJobMonitor->start(OptionsFixture::create());
    }

    public function testNextThrowsExceptionWhenNotStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monitor NOT started!');

        $importJobMonitor = self::getContainer()->get(ImportJobMonitor::class);
        $importJobMonitor->next();
    }

    public function testStopThrowsExceptionWhenNotStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Monitor NOT started!');

        $importJobMonitor = self::getContainer()->get(ImportJobMonitor::class);
        $importJobMonitor->stop(ResultCode::SUCCESS);
    }
}
