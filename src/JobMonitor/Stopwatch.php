<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\JobMonitor;

final class Stopwatch
{
    private ?bool $started = null;
    private float $startTime = 0;
    private ?float $endTime = null;
    private ?int $memory = null;

    public function isStarted(): bool
    {
        return (bool) $this->started;
    }

    public function start(): void
    {
        if ($this->started) {
            throw new \RuntimeException('Stopwatch already started.');
        }
        if ($this->started === false) {
            throw new \RuntimeException("Stopwatch can't be restarted.");
        }

        $this->started = true;
        $this->startTime = microtime(true);
    }

    public function stop(): void
    {
        if (!$this->started) {
            throw new \RuntimeException('Stopwatch NOT started.');
        }

        $this->started = false;
        $this->endTime = microtime(true);
        $this->memory = memory_get_usage(true);
    }

    public function memory(): int
    {
        return $this->memory ?? memory_get_usage(true);
    }

    public function runtime(): float
    {
        return ($this->endTime ?? microtime(true)) - $this->startTime;
    }
}
