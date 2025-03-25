<?php
declare(strict_types=1);

namespace Mfonte\PropAccessor;

use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Mfonte\PropAccessor\Exception\InvalidPropertyException;
use Mfonte\PropAccessor\Exception\MismatchedPropertiesException;
use Mfonte\PropAccessor\Exception\NoSuchPropertyException;

/**
 * Trait that turns accessors and mutators into real properties via magic methods.
 *
 * @package Mfonte\PropAccessor
 * @author Corey Frenette
 * @author Maurizio Fonte
 * @copyright Copyright (c) 2019 Corey Frenette, Copyright (c) 2024 Maurizio Fonte
 */
trait PropifierTrait
{
    /**
     * A shared method map cache for all instances.
     *
     * @var array
     */
    private static array $methodMap = [];

    /**
     * Magic getter to access properties via their accessors.
     *
     * @param string $property The property name.
     *
     * @return mixed The value of the property.
     *
     * @throws NoSuchPropertyException
     * @throws MismatchedPropertiesException
     * @throws InvalidPropertyException
     */
    public function __get(string $property): mixed
    {
        self::buildMethodMap();

        $class = get_class($this);
        if (!isset(self::$methodMap[$class][$property]['get'])) {
            throw new NoSuchPropertyException($property);
        }

        $method = self::$methodMap[$class][$property]['get'];

        if ($method instanceof ArrayProperty) {
            $method->this($this);
            return $method;
        }

        return $this->{$method}();
    }

    /**
     * Magic setter to set properties via their mutators.
     *
     * @param string $property The property name.
     * @param mixed $value The value to set.
     *
     * @throws NoSuchPropertyException
     * @throws MismatchedPropertiesException
     * @throws InvalidPropertyException
     */
    public function __set(string $property, $value): void
    {
        self::buildMethodMap();

        $class = get_class($this);
        if (!isset(self::$methodMap[$class][$property]['set'])) {
            throw new NoSuchPropertyException($property);
        }

        $method = self::$methodMap[$class][$property]['set'];

        $this->{$method}($value);
    }

    /**
     * Magic isset to check if a property is set.
     *
     * @param string $property The property name.
     *
     * @return bool True if set, false otherwise.
     *
     * @throws MismatchedPropertiesException
     * @throws InvalidPropertyException
     */
    public function __isset(string $property): bool
    {
        self::buildMethodMap();

        $class = get_class($this);

        return isset(self::$methodMap[$class][$property]['get']);
    }

    /**
     * Magic unset to unset a property.
     *
     * @param string $property The property name.
     *
     * @throws NoSuchPropertyException
     */
    public function __unset(string $property): void
    {
        self::buildMethodMap();

        $class = get_class($this);
        if (!isset(self::$methodMap[$class][$property]['unset'])) {
            throw new NoSuchPropertyException($property);
        }

        $method = self::$methodMap[$class][$property]['unset'];

        $this->{$method}();
    }

    /**
     * Builds the method map for the class if it doesn't exist.
     *
     * @throws MismatchedPropertiesException
     * @throws InvalidPropertyException
     */
    private function buildMethodMap(): void
    {
        $class = get_class($this);

        if (isset(self::$methodMap[$class])) {
            return;
        }

        try {
            $reflection = new ReflectionClass($this);
        } catch (\ReflectionException $e) {
            throw new RuntimeException('Reflection failed.', 0, $e);
        }

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED);

        $methodMap = [];

        foreach ($methods as $method) {
            $name = $method->name;
            $prefix = substr($name, 0, 3);
            $propertyName = '';

            if ($prefix === 'get' || $prefix === 'set' || $prefix === 'itr') {
                $propertyName = $this->camelToSnake(substr($name, 3));
            } elseif (substr($name, 0, 2) === 'is' || substr($name, 0, 3) === 'has') {
                $prefix = substr($name, 0, 2);
                $propertyName = $this->camelToSnake(substr($name, strlen($prefix)));
            } elseif ($prefix === 'uns') {
                $prefix = 'unset';
                $propertyName = $this->camelToSnake(substr($name, 3));
            } else {
                continue;
            }

            if (!isset($methodMap[$propertyName])) {
                $methodMap[$propertyName] = ['get' => null, 'set' => null, 'itr' => null, 'unset' => null];
            }

            if ($prefix === 'get' || $prefix === 'is' || $prefix === 'has') {
                $methodMap[$propertyName]['get'] = $method;
            } elseif ($prefix === 'set') {
                $methodMap[$propertyName]['set'] = $method;
            } elseif ($prefix === 'itr') {
                $methodMap[$propertyName]['itr'] = $method;
            } elseif ($prefix === 'unset') {
                $methodMap[$propertyName]['unset'] = $method;
            }
        }

        // Custom property mappings
        if (property_exists($this, 'propertyMap') && is_array($this::$propertyMap)) {
            foreach ($this::$propertyMap as $propName => $methods) {
                if (!isset($methodMap[$propName])) {
                    $methodMap[$propName] = ['get' => null, 'set' => null, 'itr' => null, 'unset' => null];
                }
                foreach ($methods as $type => $methodName) {
                    if (method_exists($this, $methodName)) {
                        $methodReflection = new ReflectionMethod($this, $methodName);
                        $methodMap[$propName][$type] = $methodReflection;
                    }
                }
            }
        }

        self::$methodMap[$class] = $this->extractProperties($methodMap);
    }

    /**
     * Extracts the properties from the method map.
     *
     * @param array $methodMap The method map.
     *
     * @return array The extracted properties.
     *
     * @throws MismatchedPropertiesException
     * @throws InvalidPropertyException
     */
    private function extractProperties(array $methodMap): array
    {
        $extracted = [];

        foreach ($methodMap as $name => $methods) {
            $hasGet = $methods['get'] !== null;
            $hasSet = $methods['set'] !== null;

            if ($hasGet && $hasSet) {
                $getParams = $methods['get']->getNumberOfParameters();
                $setParams = $methods['set']->getNumberOfParameters();

                if ($getParams === 0 && $setParams === 1) {
                    // Regular property
                    $extracted[$name] = ['get' => $methods['get']->name, 'set' => $methods['set']->name];
                } elseif ($getParams === 1 && $setParams === 2) {
                    // Array property
                    $arrayProperty = new ArrayProperty($methods['get'], $methods['set'], $methods['itr'] ?? null);
                    $extracted[$name] = ['get' => $arrayProperty, 'set' => $arrayProperty];
                } else {
                    throw new MismatchedPropertiesException($methods['get'], $methods['set']);
                }
            } elseif ($hasGet) {
                $getParams = $methods['get']->getNumberOfParameters();

                if ($getParams === 0) {
                    // Getter only property
                    $extracted[$name] = ['get' => $methods['get']->name, 'set' => null];
                } elseif ($getParams === 1) {
                    // Getter only array property
                    $arrayProperty = new ArrayProperty($methods['get'], null, $methods['itr'] ?? null);
                    $extracted[$name] = ['get' => $arrayProperty, 'set' => null];
                } else {
                    throw new InvalidPropertyException($methods['get']);
                }
            } elseif ($hasSet) {
                $setParams = $methods['set']->getNumberOfParameters();

                if ($setParams === 1) {
                    // Setter only property
                    $extracted[$name] = ['get' => null, 'set' => $methods['set']->name];
                } elseif ($setParams === 2) {
                    // Setter only array property
                    $arrayProperty = new ArrayProperty(null, $methods['set'], $methods['itr'] ?? null);
                    $extracted[$name] = ['get' => $arrayProperty, 'set' => null];
                } else {
                    throw new InvalidPropertyException($methods['set']);
                }
            }
        }

        return $extracted;
    }

    /**
     * Converts camelCase to snake_case.
     *
     * @param string $input The input string.
     *
     * @return string The snake_case string.
     */
    private function camelToSnake(string $input): string
    {
        $pattern = '/(?<=\\w)(?=[A-Z])/';
        return strtolower(preg_replace($pattern, '_', $input));
    }
}
