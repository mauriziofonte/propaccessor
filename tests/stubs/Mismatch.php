<?php

declare(strict_types=1);

class Mismatch
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $something;

    protected function getSomething($index)
    {
        return $this->something;
    }

    protected function setSomething($val)
    {
        $this->something = $val;
    }
}
