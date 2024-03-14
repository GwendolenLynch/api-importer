<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Reader;

use Camelot\ApiImporter\Reader\AbstractReader;
use Camelot\ApiImporter\Reader\TsvReader;
use Camelot\ApiImporter\Tests\Fixtures\Fixture;
use Camelot\ApiImporter\Tests\Fixtures\Options\OptionsFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** @internal */
#[CoversClass(TsvReader::class)]
#[CoversClass(AbstractReader::class)]
final class TsvReaderTest extends TestCase
{
    public static function providerFiles(): iterable
    {
        $expected = [
            [
                '42',
                '4.2',
                'A string',
                'A longer field that is stored in a text field',
            ],
            [
                '24',
                '2.4',
                'Another string',
                'The End',
            ],
        ];

        yield 'No Headers' => ['two-line-no-headers.tsv', null, $expected];
        yield 'Headers' => ['two-line-headers.tsv', 0, $expected];
    }

    #[DataProvider('providerFiles')]
    public function testReadFile(string $filename, ?int $headerOffset, array $expected): void
    {
        $options = OptionsFixture::create()
            ->withFilePathname(Fixture::dataFilePath($filename))
            ->withHeaderOffset($headerOffset)
        ;
        $reader = new TsvReader();
        $data = iterator_to_array($reader->readFile($options->getFilePathname(), $options->getHeaderOffset()));

        self::assertCount(2, $data);
        self::assertEqualsCanonicalizing($expected, $data);
    }

    public function testSupports(): void
    {
        $reader = new TsvReader();

        self::assertTrue($reader->supports('tsv'));
        self::assertFalse($reader->supports('csv'));
    }
}
