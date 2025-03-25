<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class CustomMapping
{
    use PropifierTrait;

    protected static array $propertyMap = [
        'customProperty' => ['get' => 'retrieveCustomProperty', 'set' => 'storeCustomProperty'],
    ];

    private string $customProperty;

    protected function retrieveCustomProperty(): string
    {
        return $this->customProperty;
    }

    protected function storeCustomProperty(string $value): void
    {
        $this->customProperty = $value;
    }
}
