<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class CustomMethodNames
{
    use PropifierTrait;

    protected static array $propertyMap = [
        'customProperty' => ['get' => 'fetchProperty', 'set' => 'updateProperty'],
    ];

    private string $property;

    protected function fetchProperty(): string
    {
        return $this->property;
    }

    protected function updateProperty(string $value): void
    {
        $this->property = $value;
    }
}
