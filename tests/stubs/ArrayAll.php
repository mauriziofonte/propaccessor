<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class ArrayAll
{
    use PropifierTrait;

    private array $arr = [];

    public function setArr(string $key, mixed $value): void
    {
        $this->arr[$key] = $value;
    }

    public function getArr(string $key): mixed
    {
        return $this->arr[$key] ?? null;
    }

    public function itrArr(): ArrayIterator
    {
        return new ArrayIterator($this->arr);
    }
}
