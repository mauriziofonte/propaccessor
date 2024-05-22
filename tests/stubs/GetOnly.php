<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

/**
 * @property-read  string  $something
 */
class GetOnly
{
    use PropifierTrait;

    /** @var string */
    private $something = 'test';

    protected function getSomething(): string
    {
        return $this->something;
    }
}
