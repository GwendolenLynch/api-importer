<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Options;

interface OptionsInterface
{
    public static function create(): self;

    public function getFileType(): string;

    public function withFileType(string $type): self;

    public function getFilePathname(): string;

    public function withFilePathname(string $filePathname): self;

    public function getEntityClass(): ?string;

    public function withEntityClass(?string $entityClass): self;

    public function isSkipExisting(): bool;

    public function withSkipExisting(bool $skipExisting): self;

    public function getStartRow(): int;

    public function withStartRow(int $startRow): self;

    public function getHeaderOffset(): ?int;

    public function withHeaderOffset(?int $headerOffset): self;

    public function getBatchSize(): int;

    public function withBatchSize(int $batchSize): self;

    public function toArray(): array;
}
