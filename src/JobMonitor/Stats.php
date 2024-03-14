<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\JobMonitor;

final class Stats
{
    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;

    public function addCreated(): void
    {
        ++$this->created;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getUpdated(): int
    {
        return $this->updated;
    }

    public function addUpdated(): void
    {
        ++$this->updated;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function addSkipped(): void
    {
        ++$this->skipped;
    }
}
