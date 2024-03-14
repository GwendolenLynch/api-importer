<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Entity;

use Camelot\ApiImporter\Enum\ResultCode;
use Camelot\ApiImporter\Repository\ImportJobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportJobRepository::class)]
class ImportJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $entityClass = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $executed = null;

    /** Jobs runtime in milliseconds */
    #[ORM\Column(nullable: true)]
    private ?int $runtime = null;

    /** Jobs peak memory use in bytes */
    #[ORM\Column(nullable: true)]
    private ?int $memory = null;

    #[ORM\Column()]
    private int $records = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?ResultCode $result = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function setEntityClass(?string $entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getExecuted(): ?\DateTimeInterface
    {
        return $this->executed;
    }

    public function setExecuted(?\DateTimeInterface $executed): self
    {
        $this->executed = $executed;

        return $this;
    }

    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    public function setRuntime(?int $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    public function getMemory(): ?int
    {
        return $this->memory;
    }

    public function setMemory(?int $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getRecords(): int
    {
        return $this->records;
    }

    public function setRecords(int $records): self
    {
        $this->records = $records;

        return $this;
    }

    public function getResult(): ?ResultCode
    {
        return $this->result;
    }

    public function setResult(?ResultCode $result): self
    {
        $this->result = $result;

        return $this;
    }
}
