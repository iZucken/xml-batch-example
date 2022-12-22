<?php

namespace App\Features\Products\VO;

class SIWeight
{
    private float $value;
    private string $units;

    const GRAM = 'g';
    const KILOGRAM = 'kg';

    const UNITS = [
        self::GRAM => 1,
        self::KILOGRAM => 1000,
    ];

    public function __construct(string $siWeightString)
    {
        [$value, $units] = explode(' ', $siWeightString) + [null, null];
        if (empty($value) || empty($units) || !isset(self::UNITS[$units])) {
            throw new \InvalidArgumentException("Cannot parse weight string $siWeightString");
        }
        $this->value = (float)$value;
        $this->units = $units;
    }

    public function __toString(): string
    {
        return "$this->value $this->units";
    }

    public function toGrams(): float
    {
        return $this->value * self::UNITS[$this->units];
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnits(): string
    {
        return $this->units;
    }
}
