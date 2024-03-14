<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Handler;

use Camelot\ApiImporter\Enum\Outcome;

final class Result
{
    public function __construct(private object $entity, private Outcome $outcome, private string $message) {}

    public static function created(object $entity, string $message): self
    {
        return new self($entity, Outcome::CREATED, $message);
    }

    public static function updated(object $entity, string $message): self
    {
        return new self($entity, Outcome::UPDATED, $message);
    }

    public static function skipped(object $entity, string $message): self
    {
        return new self($entity, Outcome::SKIPPED, $message);
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getOutcome(): Outcome
    {
        return $this->outcome;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
