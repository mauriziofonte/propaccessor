<?php

use Mfonte\CodingStandard\ConfigurationFactory;

$config = ConfigurationFactory::fromRuleset(new \Mfonte\CodingStandard\Ruleset\LaravelRuleset());
$config->getFinder()->in([
    __DIR__.'/src',
    __DIR__.'/tests'
]);

return $config;
