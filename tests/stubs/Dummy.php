<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

/**
 * @property mixed $something
 * @property array $array
 */
class Dummy
{
    use PropifierTrait;

    private $something;
    private $another;
    private $calculator;

    /** @var array */
    private $array = [1, 2, 3, 4];

    protected function getSomething()
    {
        return $this->something;
    }

    protected function setSomething($val): void
    {
        $this->something = $val;
    }

    public function getAnother()
    {
        return $this->another;
    }

    public function setAnother($val): void
    {
        $this->another = $val;
    }

    public function getCalculator()
    {
        return $this->calculator;
    }

    public function setCalculator(int $val): void
    {
        $this->calculator = $val * 10;
    }

    protected function getArray($index)
    {
        return $this->array[$index];
    }

    protected function setArray($index, $val): void
    {
        $this->array[$index] = $val;
    }
}
