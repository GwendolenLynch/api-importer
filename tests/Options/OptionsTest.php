<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Options;

use Camelot\ApiImporter\Options\Options;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** @internal */
#[CoversClass(Options::class)]
final class OptionsTest extends TestCase
{
    public static function providerFromOptions(): iterable
    {
        yield 'withFileType' => [Options::create()->withFileType('csv')];
        yield 'withFilePathname' => [Options::create()->withFilePathname('path/to/file.csv')];
        yield 'withEntityClass' => [Options::create()->withEntityClass(EntityFixture::class)];
        yield 'withSkipExisting' => [Options::create()->withSkipExisting(true)];
        yield 'withStartRow' => [Options::create()->withStartRow(25)];
        yield 'withHeaderOffset' => [Options::create()->withHeaderOffset(1)];
        yield 'withBatchSize' => [Options::create()->withBatchSize(5)];
        yield 'All' => [
            Options::create()
                ->withFileType('tsv')
                ->withFilePathname('path/to/file.tsv')
                ->withEntityClass(EntityFixture::class)
                ->withSkipExisting(false)
                ->withStartRow(10)
                ->withHeaderOffset(0)
                ->withBatchSize(10),
        ];
    }

    #[DataProvider('providerFromOptions')]
    public function testFromOptions(Options $source): void
    {
        $options = Options::fromOptions($source);

        self::assertSame($source->toArray(), $options->toArray());
    }

    public static function providerWith(): iterable
    {
        yield ['withFileType', 'getFileType', 'tsv'];
        yield ['withFilePathname', 'getFilePathname', 'path/to/file.tsv'];
        yield ['withEntityClass', 'getEntityClass', EntityFixture::class];
        yield ['withSkipExisting', 'isSkipExisting', false];
        yield ['withStartRow', 'getStartRow', 10];
        yield ['withHeaderOffset', 'getHeaderOffset', 0];
        yield ['withBatchSize', 'getBatchSize', 10];
    }

    #[DataProvider('providerWith')]
    public function testWith(string $setter, string $getter, mixed $value): void
    {
        $options = Options::create()->{$setter}($value);

        self::assertSame($value, $options->{$getter}());
    }
}
