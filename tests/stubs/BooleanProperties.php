<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class BooleanProperties
{
    use PropifierTrait;

    private bool $active = false;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $value): void
    {
        $this->active = $value;
    }

    public function hasItems(): bool
    {
        return true;
    }
}
