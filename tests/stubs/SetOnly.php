<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

/**
 * @property-write  string  something
 */
class SetOnly
{
    use PropifierTrait;

    private $something = 'test';

    protected function setSomething($value): void
    {
        $this->something = $value;
    }
}
