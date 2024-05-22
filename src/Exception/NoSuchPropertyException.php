<?php
namespace Mfonte\PropAccessor\Exception;

use Exception;

/**
 * Indicates an accessor or mutator does not exist for a given property.
 *
 * @author Corey Frenette
 * @author Maurizio Fonte
 * @copyright Copyright (c) 2019 Corey Frenette, Copyright (c) 2024 Maurizio Fonte
 */
class NoSuchPropertyException extends Exception
{
    /**
     * The property that does not exist.
     *
     * @var string
     */
    private $property;

    public function __construct(string $property)
    {
        parent::__construct("PropAccessor: Property [{$property}] does not exist.");
        $this->property = $property;
    }

    /**
     * Get the property that does not exist.
     */
    public function getProperty(): string
    {
        return $this->property;
    }
}
