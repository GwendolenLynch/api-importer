<?php

declare(strict_types=1);

namespace Camelot\ApiImporter;

use Camelot\ApiImporter\JobMonitor\Stats;
use Camelot\ApiImporter\Options\OptionsInterface;

interface ImporterInterface
{
    /** Import records according to options. */
    public function import(OptionsInterface $options, Stats $stats): \Generator;

    /** Number of records currently processed. */
    public function count(): int;
}
