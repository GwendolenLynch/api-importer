<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Handler;

use Camelot\ApiImporter\Options\OptionsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Handlers accept an array that represents a single row of import data and
 * transforms that into a new or updated entity.
 *
 * Handlers implementing this interface:
 *  - SHOULD NOT persist created or updated entities.
 *  - SHOULD NOT call EntityManager::flush() or EntityManager::clear().
 */
#[AutoconfigureTag('camelot.api_import.handler')]
interface HandlerInterface
{
    /** @return bool If the handler supports the entity class. */
    public function supports(string $entityClass): bool;

    /** Create or update an entity based on the import record. */
    public function handle(array $row, OptionsInterface $options): Result;
}
