<?php
namespace Mfonte\PropAccessor\Exception;

use Exception;
use ReflectionMethod;

/**
 * Indicates a property has an incorrect number of parameters.
 *
 * @author Corey Frenette
 * @author Maurizio Fonte
 * @copyright Copyright (c) 2019 Corey Frenette, Copyright (c) 2024 Maurizio Fonte
 */
class InvalidPropertyException extends Exception
{
    /**
     * The invalid property.
     *
     * @var ReflectionMethod
     */
    private $property;

    public function __construct(ReflectionMethod $property)
    {
        parent::__construct("PropAccessor: Property [{$property->name}] has an invalid number of arguments.");
        $this->property = $property;
    }

    /**
     * Get the invalid property.
     */
    public function getProperty(): ReflectionMethod
    {
        return $this->property;
    }
}
