<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class ArrayItrOnly
{
    use PropifierTrait;

    private array $arr;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public function itrArr(): ArrayIterator
    {
        return new ArrayIterator($this->arr);
    }
}
