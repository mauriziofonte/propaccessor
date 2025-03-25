<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class UnsettableProperty
{
    use PropifierTrait;

    private ?string $property = null;

    public function setProperty(string $value): void
    {
        $this->property = $value;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function unsetProperty(): void
    {
        $this->property = null;
    }
}
