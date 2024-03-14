<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Fixtures\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EntityFixture
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $intField = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $floatField = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $stringField = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textField = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntField(): ?int
    {
        return $this->intField;
    }

    public function setIntField(?int $intField): self
    {
        $this->intField = $intField;

        return $this;
    }

    public function getFloatField(): ?float
    {
        return $this->floatField;
    }

    public function setFloatField(?float $floatField): self
    {
        $this->floatField = $floatField;

        return $this;
    }

    public function getStringField(): ?string
    {
        return $this->stringField;
    }

    public function setStringField(?string $stringField): self
    {
        $this->stringField = $stringField;

        return $this;
    }

    public function getTextField(): ?string
    {
        return $this->textField;
    }

    public function setTextField(?string $textField): self
    {
        $this->textField = $textField;

        return $this;
    }
}
