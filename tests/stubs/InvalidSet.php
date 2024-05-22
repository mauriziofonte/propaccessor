<?php

declare(strict_types=1);

class InvalidSet
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $something = 'test';

    protected function setSomething($value, $a, $b)
    {
        $this->something = $value;
    }
}
