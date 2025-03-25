<?php

declare(strict_types=1);

use Mfonte\PropAccessor\PropifierTrait;

class UnmappedProperties
{
    use PropifierTrait;

    private string $unmappedProperty = 'value';
}
