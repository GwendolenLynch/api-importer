<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Options;

trait OptionsTrait
{
    private string $fileType;
    private string $filePathname;
    private ?string $entityClass = null;
    private bool $skipExisting = false;
    private int $startRow = 0;
    private ?int $headerOffset = null;
    private int $batchSize = 10000;

    public static function create(): self
    {
        return new self();
    }

    public static function fromOptions(OptionsInterface $options): self
    {
        $self = new self();
        foreach ($options->toArray() as $property => $value) {
            $self->{$property} = $value;
        }

        return $self;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }

    public function withFileType(string $type): self
    {
        $clone = clone $this;
        $clone->fileType = $type;

        return $clone;
    }

    public function getFilePathname(): string
    {
        return $this->filePathname;
    }

    public function withFilePathname(string $filePathname): self
    {
        $clone = clone $this;
        $clone->filePathname = $filePathname;

        return $clone;
    }

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function withEntityClass(?string $entityClass): self
    {
        $clone = clone $this;
        $clone->entityClass = $entityClass;

        return $clone;
    }

    public function isSkipExisting(): bool
    {
        return $this->skipExisting;
    }

    public function withSkipExisting(bool $skipExisting): self
    {
        $clone = clone $this;
        $clone->skipExisting = $skipExisting;

        return $clone;
    }

    public function getStartRow(): int
    {
        return $this->startRow;
    }

    public function withStartRow(int $startRow): self
    {
        $clone = clone $this;
        $clone->startRow = $startRow;

        return $clone;
    }

    public function getHeaderOffset(): ?int
    {
        return $this->headerOffset;
    }

    public function withHeaderOffset(?int $headerOffset): self
    {
        $clone = clone $this;
        $clone->headerOffset = $headerOffset;

        return $clone;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function withBatchSize(int $batchSize): self
    {
        $clone = clone $this;
        $clone->batchSize = $batchSize;

        return $clone;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
