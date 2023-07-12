<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Reader;

use League\Csv\TabularDataReader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('camelot.api_import.reader')]
interface ReaderInterface
{
    public function supports(string $type): bool;

    /** @return TabularDataReader */
    public function readFile(string $filepath, ?int $headerOffset = null): iterable;
}
