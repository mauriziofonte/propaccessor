<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class GetOnly
{
    use PropifierTrait;

    public function getSomething(): string
    {
        return 'test';
    }
}
