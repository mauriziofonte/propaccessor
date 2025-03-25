<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class ArrayGetOnly
{
    use PropifierTrait;

    private array $arrayProperty = ['key' => 'value'];

    public function getArrayProperty(string $key): mixed
    {
        return $this->arrayProperty[$key] ?? null;
    }

    public function itrArrayProperty(): ArrayIterator
    {
        return new ArrayIterator($this->arrayProperty);
    }
}
