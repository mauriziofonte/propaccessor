<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class InvalidGet
{
    use PropifierTrait;

    public function getSomething(string $param1, string $param2): string
    {
        return 'value';
    }
}
