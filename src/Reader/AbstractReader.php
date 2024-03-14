<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Reader;

use League\Csv\AbstractCsv;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\TabularDataReader;

abstract class AbstractReader implements ReaderInterface
{
    /** @return TabularDataReader */
    public function readFile(string $filepath, ?int $headerOffset = null): iterable
    {
        $reader = Reader::createFromStream(fopen($filepath, 'r'));

        $this->setDelimiter($reader);

        if ($headerOffset !== null) {
            $reader->setHeaderOffset($headerOffset);
        }

        yield from Statement::create()->offset((int) $headerOffset)->process($reader);
    }

    abstract protected function setDelimiter(AbstractCsv $reader): void;
}
