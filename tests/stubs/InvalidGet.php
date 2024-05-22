<?php

declare(strict_types=1);

class InvalidGet
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $something = 'test';

    protected function getSomething($a, $b)
    {
        return $this->something;
    }
}
