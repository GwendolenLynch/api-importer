<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests;

use Camelot\ApiImporter\DataImporter;
use Camelot\ApiImporter\Entity\ImportJob;
use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\JobMonitor\Stats;
use Camelot\ApiImporter\Repository\ImportJobRepository;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;
use Camelot\ApiImporter\Tests\Fixtures\Fixture;
use Camelot\ApiImporter\Tests\Fixtures\Options\OptionsFixture;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/** @internal */
#[CoversClass(DataImporter::class)]
#[CoversClass(ImportJob::class)]
#[CoversClass(ImportJobRepository::class)]
final class DataImporterTest extends FunctionalTestCase
{
    public function testImport(): void
    {
        $this->runImport(2, 'two-line-headers.csv', 0);

        $repo = self::getContainer()->get(EntityManagerInterface::class)->getRepository(EntityFixture::class);
        $entities = $repo->findAll();

        self::assertCount(2, $entities);
        self::assertSame(42, $entities[0]->getIntField());
        self::assertSame(2.4, $entities[1]->getFloatField());
    }

    public function testImportJobs(): void
    {
        $this->runImport(2, 'two-line-headers.csv', 0);

        $repo = self::getContainer()->get(EntityManagerInterface::class)->getRepository(ImportJob::class);
        $entities = $repo->findAll();

        self::assertCount(1, $entities);
        self::assertSame(2, $entities[0]->getRecords());
        self::assertSame(EntityFixture::class, $entities[0]->getEntityClass());
        self::assertInstanceOf(DateTimeInterface::class, $entities[0]->getExecuted());
        self::assertIsInt($entities[0]->getRuntime());
        self::assertIsInt($entities[0]->getMemory());;
        self::assertSame(2, $entities[0]->getRecords());
        self::assertSame(ResultCode::SUCCESS, $entities[0]->getResult());
    }

    private function runImport(?int $expectedImportCount, string $filename, ?int $headerOffset): void
    {
        $dataImporter = self::getContainer()->get(DataImporter::class);
        $options = OptionsFixture::create()
            ->withFilePathname(Fixture::dataFilePath($filename))
            ->withHeaderOffset($headerOffset)
        ;
        $stats = new Stats();

        iterator_to_array($dataImporter->import($options, $stats));

        self::assertSame($expectedImportCount, $dataImporter->count());
    }
}
