<?php

namespace App\DTOs;

readonly class CreateEventDTO
{
    public function __construct(
        public string $name,
        public int $capacity
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            capacity: (int) $data['capacity']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'capacity' => $this->capacity,
        ];
    }
}


