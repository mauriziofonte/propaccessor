<?php

declare(strict_types=1);

class ArrayGetOnly
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $something = ['test' => 'test'];

    protected function getSomething($index)
    {
        return $this->something[$index];
    }
}
