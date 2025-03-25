<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class Dummy
{
    use PropifierTrait;

    private string $something;
    private string $another;
    private int $calculator;
    private array $array = [];

    public function setSomething(string $value): void
    {
        $this->something = $value;
    }

    public function getSomething(): string
    {
        return $this->something;
    }

    public function setAnother(string $value): void
    {
        $this->another = $value;
    }

    public function getAnother(): string
    {
        return $this->another;
    }

    public function setCalculator(int $value): void
    {
        $this->calculator = $value * 10;
    }

    public function getCalculator(): int
    {
        return $this->calculator;
    }

    public function setArray(int $index, mixed $value): void
    {
        $this->array[$index] = $value;
    }

    public function getArray(int $index): mixed
    {
        return $this->array[$index] ?? null;
    }

    public function itrArray(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }
}
