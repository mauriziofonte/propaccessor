<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class ArraySetOnly
{
    use PropifierTrait;

    private array $arrayProperty = [];

    public function setArrayProperty(string $key, mixed $value): void
    {
        $this->arrayProperty[$key] = $value;
    }
}
