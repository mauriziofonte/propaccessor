<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class InvalidSet
{
    use PropifierTrait;

    public function setSomething(string $param1, string $param2): void
    {
        // Invalid setter with too many parameters
    }
}
