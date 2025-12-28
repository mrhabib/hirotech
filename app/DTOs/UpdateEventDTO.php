<?php

namespace App\DTOs;

readonly class UpdateEventDTO
{
    public function __construct(
        public ?string $name = null,
        public ?int $capacity = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            capacity: isset($data['capacity']) ? (int) $data['capacity'] : null
        );
    }

    public function toArray(): array
    {
        $array = [];
        if ($this->name !== null) {
            $array['name'] = $this->name;
        }
        if ($this->capacity !== null) {
            $array['capacity'] = $this->capacity;
        }
        return $array;
    }
}


