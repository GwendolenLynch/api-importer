<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Fixtures\Handler;

use Camelot\ApiImporter\Handler\HandlerInterface;
use Camelot\ApiImporter\Handler\Result;
use Camelot\ApiImporter\Options\OptionsInterface;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('camelot.api_import.handler')]
final class HandlerFixture implements HandlerInterface
{
    public function supports(string $entityClass): bool
    {
        return $entityClass === EntityFixture::class;
    }

    public function handle(array $row, OptionsInterface $options): Result
    {
        $row = array_values($row);
        $entity = new EntityFixture();
        $entity
            ->setIntField((int) $row[0])
            ->setFloatField((float) $row[1])
            ->setStringField($row[2])
            ->setTextField($row[3])
        ;

        return Result::created($entity, 'testing');
    }
}
