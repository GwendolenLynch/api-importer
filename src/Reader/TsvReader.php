<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Reader;

use League\Csv\AbstractCsv;

final class TsvReader extends AbstractReader
{
    public function supports(string $type): bool
    {
        return strtolower($type) === 'tsv';
    }

    protected function setDelimiter(AbstractCsv $reader): void
    {
        $reader->setDelimiter("\t");
    }
}
