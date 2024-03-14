<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Reader;

use League\Csv\AbstractCsv;

final class CsvReader extends AbstractReader
{
    public function supports(string $type): bool
    {
        return strtolower($type) === 'csv';
    }

    protected function setDelimiter(AbstractCsv $reader): void {}
}
