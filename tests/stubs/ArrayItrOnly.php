<?php

declare(strict_types=1);

class ArrayItrOnly
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $arr;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    protected function itrArr()
    {
        return new ArrayIterator($this->arr);
    }
}
