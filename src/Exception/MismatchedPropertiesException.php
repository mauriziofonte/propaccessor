<?php
namespace Mfonte\PropAccessor\Exception;

use Exception;
use ReflectionMethod;

/**
 * Indicates a property has mismatched arguments in the accessor and mutator.
 *
 * @author Corey Frenette
 * @author Maurizio Fonte
 * @copyright Copyright (c) 2019 Corey Frenette, Copyright (c) 2024 Maurizio Fonte
 */
class MismatchedPropertiesException extends Exception
{
    /**
     * The accessor.
     *
     * @var ReflectionMethod|null
     */
    private $get;

    /**
     * The mutator.
     *
     * @var ReflectionMethod|null
     */
    private $set;

    public function __construct(ReflectionMethod $get = null, ReflectionMethod $set = null)
    {
        if ($get !== null) {
            $name = $get->name;
        } elseif ($set !== null) {
            $name = $set->name;
        } else {
            $name = '';
        }

        parent::__construct("PropAccessor: Declaration of property [{$name}] is inconsistent.");

        $this->get = $get;
        $this->set = $set;
    }

    /**
     * Get the accessor.
     */
    public function getGet(): ReflectionMethod
    {
        return $this->get;
    }

    /**
     * Get the mutator.
     */
    public function getSet(): ReflectionMethod
    {
        return $this->set;
    }
}
