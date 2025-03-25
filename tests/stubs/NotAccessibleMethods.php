<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class NonAccessibleMethods
{
    use PropifierTrait;

    private string $property = 'value';

    private function getProperty(): string
    {
        return $this->property;
    }
}
