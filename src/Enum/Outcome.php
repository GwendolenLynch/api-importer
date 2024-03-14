<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Enum;

enum Outcome: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case SKIPPED = 'skipped';
}
