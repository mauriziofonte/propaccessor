<?php

declare(strict_types=1);

class ArraySetOnly
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $something = ['test'];

    protected function setSomething($index, $value)
    {
        $this->something[$index] = $value;
    }
}
