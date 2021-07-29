<?php

namespace Wijourdil\LaravelAccountLock\Classes;

class Account
{
    public function __construct(
        private string $table,
        private string $identifierName,
        private int $identifierValue,
        private string $type,
    ) {
    }

    public static function fromArray(array $array): self
    {
        return new self(...$array);
    }

    public function toArray(): array
    {
        return [
            'table' => $this->table,
            'identifierName' => $this->identifierName,
            'identifierValue' => $this->identifierValue,
            'type' => $this->type,
        ];
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getIdentifierName(): string
    {
        return $this->identifierName;
    }

    public function getIdentifierValue(): int
    {
        return $this->identifierValue;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
