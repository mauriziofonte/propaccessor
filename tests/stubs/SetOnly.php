<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class SetOnly
{
    use PropifierTrait;

    private string $something;

    public function setSomething(string $value): void
    {
        $this->something = $value;
    }
}
