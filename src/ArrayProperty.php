<?php
declare(strict_types=1);

namespace Mfonte\PropAccessor;

use ArrayAccess;
use IteratorAggregate;
use ReflectionMethod;
use Traversable;
use Mfonte\PropAccessor\Exception\NoSuchPropertyException;

/**
 * A magic accessor/mutator for dealing with array-like properties.
 *
 * @package Mfonte\PropAccessor
 * @author Corey Frenette
 * @author Maurizio Fonte
 * @copyright Copyright (c) 2019 Corey Frenette, Copyright (c) 2024 Maurizio Fonte
 */
class ArrayProperty implements ArrayAccess, IteratorAggregate
{
    /**
     * The name of the property.
     *
     * @var string
     */
    private string $name;

    /**
     * The object instance we are getting the properties from.
     *
     * @var object
     */
    private object $obj;

    /**
     * The accessor method.
     *
     * @var ReflectionMethod|null
     */
    private ?ReflectionMethod $get;

    /**
     * The mutator method.
     *
     * @var ReflectionMethod|null
     */
    private ?ReflectionMethod $set;

    /**
     * The iterator method.
     *
     * @var ReflectionMethod|null
     */
    private ?ReflectionMethod $iterator;

    /**
     * Constructor for ArrayProperty.
     *
     * @param ReflectionMethod|null $get The accessor method, if one is defined.
     * @param ReflectionMethod|null $set The mutator method, if one is defined.
     * @param ReflectionMethod|null $iterator The iterator method, if one is defined.
     */
    public function __construct(?ReflectionMethod $get = null, ?ReflectionMethod $set = null, ?ReflectionMethod $iterator = null)
    {
        $this->get = $get;
        $this->set = $set;
        $this->iterator = $iterator;

        if ($get !== null) {
            $this->name = $get->name;
            $get->setAccessible(true);
        }

        if ($set !== null) {
            $this->name = $set->name;
            $set->setAccessible(true);
        }

        if ($iterator !== null) {
            $this->name = $iterator->name;
            $iterator->setAccessible(true);
        }
    }

    /**
     * Updates the object instance.
     *
     * @param object $obj The object instance.
     */
    public function this(object $obj): void
    {
        $this->obj = $obj;
    }

    /**
     * Executes the accessor for a given array property.
     *
     * @param mixed $offset The index into the array to access.
     *
     * @return mixed The return value of the accessor.
     *
     * @throws NoSuchPropertyException If there is no accessor for this array property.
     */
    public function offsetGet($offset): mixed
    {
        if ($this->get !== null) {
            return $this->get->invoke($this->obj, $offset);
        }

        throw new NoSuchPropertyException($this->name);
    }

    /**
     * Executes the mutator for a given array property.
     *
     * @param mixed $offset The index into the array to mutate.
     * @param mixed $value The new value.
     *
     * @throws NoSuchPropertyException If there is no mutator for this array property.
     */
    public function offsetSet($offset, $value): void
    {
        if ($this->set !== null) {
            $this->set->invoke($this->obj, $offset, $value);
            return;
        }

        throw new NoSuchPropertyException($this->name);
    }

    /**
     * Checks if an offset exists.
     *
     * @param mixed $offset The index to check.
     *
     * @return bool Always returns false.
     */
    public function offsetExists($offset): bool
    {
        return false;
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The index to unset.
     */
    public function offsetUnset($offset): void
    {
        // No action taken.
    }

    /**
     * Returns the iterator for the array property.
     *
     * @return Traversable The iterator.
     *
     * @throws NoSuchPropertyException If there is no iterator for this array property.
     */
    public function getIterator(): Traversable
    {
        if ($this->iterator !== null) {
            return $this->iterator->invoke($this->obj);
        }

        throw new NoSuchPropertyException($this->name);
    }
}
