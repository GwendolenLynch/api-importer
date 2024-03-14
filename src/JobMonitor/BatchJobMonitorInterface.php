<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\JobMonitor;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Handler\Result;
use Camelot\ApiImporter\Options\OptionsInterface;

interface BatchJobMonitorInterface
{
    public function isStarted(): bool;

    public function start(OptionsInterface $options, Stats $stats): void;

    /** @return bool True if a flush occurred, false otherwise. */
    public function next(Result $result): bool;

    public function stop(ResultCode $resultCode): void;
}
