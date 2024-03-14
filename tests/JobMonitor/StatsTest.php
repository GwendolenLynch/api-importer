<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\JobMonitor;

use Camelot\ApiImporter\JobMonitor\Stats;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** @internal */
#[CoversClass(Stats::class)]
final class StatsTest extends TestCase
{
    public function testCreated(): void
    {
        $stats = new Stats();

        self::assertSame(0, $stats->getCreated());
        $stats->addCreated();
        self::assertSame(1, $stats->getCreated());
    }

    public function testUpdated(): void
    {
        $stats = new Stats();

        self::assertSame(0, $stats->getUpdated());
        $stats->addUpdated();
        self::assertSame(1, $stats->getUpdated());
    }

    public function testSkipped(): void
    {
        $stats = new Stats();

        self::assertSame(0, $stats->getSkipped());
        $stats->addSkipped();
        self::assertSame(1, $stats->getSkipped());
    }
}
