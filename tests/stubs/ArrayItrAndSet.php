<?php

declare(strict_types=1);

class ArrayItrAndSet
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $arr;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    protected function setArr($index, $value)
    {
        $this->arr[$index] = $value;
    }

    protected function itrArr()
    {
        return new ArrayIterator($this->arr);
    }
}
