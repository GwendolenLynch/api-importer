<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Handler;

use Camelot\ApiImporter\Enum\Outcome;
use Camelot\ApiImporter\Handler\Result;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** @internal */
#[CoversClass(Result::class)]
final class ResultTest extends TestCase
{
    public function testCreated(): void
    {
        $entity = new EntityFixture();
        $result = Result::created($entity, 'This was created');

        self::assertSame(Outcome::CREATED, $result->getOutcome());
        self::assertSame($entity, $result->getEntity());
        self::assertSame('This was created', $result->getMessage());
    }

    public function testUpdated(): void
    {
        $entity = new EntityFixture();
        $result = Result::updated($entity, 'This was updated');

        self::assertSame(Outcome::UPDATED, $result->getOutcome());
        self::assertSame($entity, $result->getEntity());
        self::assertSame('This was updated', $result->getMessage());
    }

    public function testSkipped(): void
    {
        $entity = new EntityFixture();
        $result = Result::skipped($entity, 'This was skipped');

        self::assertSame(Outcome::SKIPPED, $result->getOutcome());
        self::assertSame($entity, $result->getEntity());
        self::assertSame('This was skipped', $result->getMessage());
    }
}
