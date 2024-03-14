<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Fixtures\Command;

use Camelot\ApiImporter\Command\AbstractImportCommand;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('app:import:test')]
final class CommandFixture extends AbstractImportCommand
{
    protected function getEntityClass(): string
    {
        return EntityFixture::class;
    }

    protected function getFileArgumentDescription(): string
    {
        return 'A test file';
    }
}
