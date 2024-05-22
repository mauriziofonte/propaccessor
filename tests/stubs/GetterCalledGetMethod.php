<?php

declare(strict_types=1);

class GetterCalledGetMethod
{
    use Mfonte\PropAccessor\PropifierTrait;

    private $method = 'test';

    protected function getMethod()
    {
        return $this->method;
    }
}
