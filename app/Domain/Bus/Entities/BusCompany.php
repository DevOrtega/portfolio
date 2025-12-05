<?php

namespace App\Domain\Bus\Entities;

/**
 * Domain Entity: BusCompany
 * 
 * Represents a bus company (Guaguas Municipales, Global, etc.)
 */
final readonly class BusCompany
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public string $primaryColor,
        public string $secondaryColor,
        public string $textColor
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'text_color' => $this->textColor,
        ];
    }
}
