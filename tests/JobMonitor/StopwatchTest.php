<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\JobMonitor;

use Camelot\ApiImporter\JobMonitor\Stopwatch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** @internal */
#[CoversClass(Stopwatch::class)]
final class StopwatchTest extends TestCase
{
    public function testStartStop(): void
    {
        $stopwatch = new Stopwatch();
        self::assertFalse($stopwatch->isStarted());

        $stopwatch->start();
        self::assertTrue($stopwatch->isStarted());

        $stopwatch->stop();
        self::assertFalse($stopwatch->isStarted());
    }

    public function testMemory(): void
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start();
        $stopwatch->stop();

        self::assertIsInt($stopwatch->memory());
    }

    public function testRuntime(): void
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start();
        $stopwatch->stop();

        self::assertIsFloat($stopwatch->runtime());
    }

    public function testStartThrowsExceptionWhenAlreadyStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Stopwatch already started.');

        $stopwatch = new Stopwatch();
        $stopwatch->start();
        $stopwatch->start();
    }

    public function testStartThrowsExceptionWhenAlreadyStopped(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Stopwatch can't be restarted.");

        $stopwatch = new Stopwatch();
        $stopwatch->start();
        $stopwatch->stop();
        $stopwatch->start();
    }

    public function testStopThrowsExceptionWhenNotStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Stopwatch NOT started.');

        $stopwatch = new Stopwatch();
        $stopwatch->stop();
    }
}
