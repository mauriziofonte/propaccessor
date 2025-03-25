<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class Mismatch
{
    use PropifierTrait;

    public function getSomething(): string
    {
        return 'value';
    }

    public function setSomething(string $value, string $extra): void
    {
        // Incorrect setter signature
    }
}
