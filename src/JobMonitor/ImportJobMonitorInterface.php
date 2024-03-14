<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\JobMonitor;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Options\OptionsInterface;

interface ImportJobMonitorInterface
{
    public function isStarted(): bool;

    public function start(OptionsInterface $options): void;

    public function next(): void;

    public function stop(ResultCode $result): void;

    public function count(): int;
}
