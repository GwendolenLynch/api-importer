API Data Importer
=================

Handlers
--------

Handlers accept an array that represents a single row of import data and
transforms that into a new or updated entity.

Handlers 
  - MUST implement `Camelot\ApiImporter\Handler\HandlerInterface`.
  - SHOULD NOT persist created or updated entities.
  - SHOULD NOT call EntityManager::flush() or EntityManager::clear().

```php 
<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Squirrel;
use App\Repository\SquirrelRepository;
use Camelot\ApiImporter\Handler\HandlerInterface;
use Camelot\ApiImporter\Handler\Result;
use Camelot\ApiImporter\Options\OptionsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('camelot.api_import.handler')]
final class SquirrelHandler implements HandlerInterface
{
    public function __construct(
        private SquirrelRepository $repository,
    ) {}

    public function supports(string $entityClass): bool
    {
        return $entityClass === Squirrel::class;
    }

    public function handle(array $row, OptionsInterface $options): Result
    {
        if ($entity = $this->repository->findOneBy(['name' => $row[2]])) {
            if ($options->isSkipExisting()) {
                return Result::skipped($entity, 'Skipped record for ' . $entity->getName());
            }

            return Result::updated($entity, 'Updated record for ' . $entity->getName());
        }

        $row = array_values($row);
        $entity = new Squirrel();
        $entity
            ->setAge((int) $row[0])
            ->setWeight((float) $row[1])
            ->setName($row[2])
        ;

        return Result::created($entity, 'Created record for ' . $entity->getName());
    }
}
```

Commands
--------

Optional commands can extend `Camelot\ApiImporter\Command\AbstractImportCommand`.

```php
<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Squirrel;
use Camelot\ApiImporter\Command\AbstractImportCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('app:import:squirrel')]
final class CommandFixture extends AbstractImportCommand
{
    protected function getEntityClass(): string
    {
        return Squirrel::class;
    }

    protected function getFileArgumentDescription(): string
    {
        return 'A CSV file that contains squirrel data to import.';
    }
}
```
