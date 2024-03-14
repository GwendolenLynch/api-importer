<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Enum;

enum ResultCode: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case FAILURE = 'failure';
}
