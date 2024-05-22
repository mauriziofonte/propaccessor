<?php
namespace Mfonte\PropAccessor;

use Mfonte\PropAccessor\Exception\InvalidPropertyException;
use Mfonte\PropAccessor\Exception\MismatchedPropertiesException;
use Mfonte\PropAccessor\Exception\NoSuchPropertyException;
use function get_class;
use ICanBoogie\Inflector;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use function strlen;

/**
 * Turns regular accessors and mutators into real properties.
 *
 * @author Corey Frenette
 * @author Maurizio Fonte
 * @copyright Copyright (c) 2019 Corey Frenette, Copyright (c) 2024 Maurizio Fonte
 */
trait PropifierTrait
{
    /**
     * A property cache shared between all instances.
     *
     * @var array
     */
    private static $methodMap = [];

    /**
     * Executes the accessor for a given property.
     *
     * @param string $property The name of the property
     *
     * @throws NoSuchPropertyException
     * @throws MismatchedPropertiesException If one property is an array property and the other isn't
     * @throws InvalidPropertyException If a property has an invalid number of arguments
     *
     * @return mixed The value of the property
     */
    public function __get(string $property)
    {
        // build dependency tree, if necessary
        self::_propAccessorBuildDeps($this);

        // check if the property exists
        $class = get_class($this);
        if (! isset(self::$methodMap[$class][$property]['get'])) {
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
     * Executes the mutator for a given property.
     *
     * @param string $property The name of the property
     * @param mixed $value The value to set the property to
     *
     * @throws NoSuchPropertyException
     * @throws MismatchedPropertiesException If one property is an array property and the other isn't
     * @throws InvalidPropertyException If a property has an invalid number of arguments
     */
    public function __set(string $property, $value): void
    {
        // build dependency tree, if necessary
        self::_propAccessorBuildDeps($this);

        // check if the property exists
        $class = get_class($this);
        if (! isset(self::$methodMap[$class][$property]['set'])) {
            throw new NoSuchPropertyException($property);
        }

        $method = self::$methodMap[$class][$property]['set'];

        $this->{$method}($value);
    }

    /**
     * Checks if a property is set.
     *
     * @param string $property The name of the property
     *
     * @throws MismatchedPropertiesException If one property is an array property and the other isn't
     * @throws InvalidPropertyException If a property has an invalid number of arguments
     *
     * @return bool True if the property exists, false otherwise
     */
    public function __isset(string $property): bool
    {
        // build dependency tree, if necessary
        self::_propAccessorBuildDeps($this);

        // check if the property exists
        $class = get_class($this);

        return isset(self::$methodMap[$class][$property]['get']);
    }

    /**
     * Builds and caches the properties for a given object, if not already cached.
     *
     * @param object $obj The object to cache
     *
     * @throws MismatchedPropertiesException If one property is an array property and the other isn't
     * @throws InvalidPropertyException If a property has an invalid number of arguments
     */
    private static function _propAccessorBuildDeps($obj): void
    {
        $name = get_class($obj);

        if (! array_key_exists($name, self::$methodMap)) {
            try {
                $class = new ReflectionClass($obj);
            } catch(ReflectionException $e) {
                throw new RuntimeException('This exception should never be thrown.', 0, $e);
            }

            $properties = array_values(array_filter($class->getMethods(), function (ReflectionMethod $method) {
                $isProtectedOrPublic = $method->isProtected() || $method->isPublic();
                $hasMinLength = strlen($method->name) > 3;
                $hasRequiredNaming = str_starts_with($method->name, 'get') || str_starts_with($method->name, 'set') || str_starts_with($method->name, 'itr');

                return $isProtectedOrPublic && $hasMinLength && $hasRequiredNaming;
            }));

            $inflector = Inflector::get();

            $methodMap = array_reduce($properties, function (array $mapped, ReflectionMethod $property) use ($inflector) {
                $prop_name = call_user_func_array([$inflector, 'underscore'], [substr($property->name, 3)]);

                if (! isset($mapped[$prop_name])) {
                    $mapped[$prop_name] = ['get' => null, 'set' => null, 'itr' => null];
                }

                $mapped[$prop_name][substr($property->name, 0, 3)] = $property;

                return $mapped;
            }, []);

            self::$methodMap[$name] = self::_propAccessorExtractProperties($methodMap);
        }
    }

    /**
     * Verifies and builds accessors/mutators for each property.
     *
     * @param ReflectionMethod[][] $properties The properties to transform
     *
     * @throws MismatchedPropertiesException If one property is an array property and the other isn't
     * @throws InvalidPropertyException If a property has an invalid number of arguments
     *
     * @return string[][][]|ArrayProperty[][][]|null[][][] The executable properties for the given object
     */
    private static function _propAccessorExtractProperties(array $properties): array
    {
        $extracted = [];

        foreach ($properties as $name => $property) {
            $hasGet = $property['get'] !== null;
            $hasSet = $property['set'] !== null;

            if ($hasGet && $hasSet) {
                if (
                    $property['get']->getNumberOfParameters() === 0 &&
                    $property['set']->getNumberOfParameters() === 1
                ) {
                    // Regular get and set
                    $extracted[$name] = ['get' => $property['get']->name, 'set' => $property['set']->name];
                } elseif (
                    $property['get']->getNumberOfParameters() === 1 &&
                    $property['set']->getNumberOfParameters() === 2
                ) {
                    // Array get and set
                    $array_property = new ArrayProperty($property['get'], $property['set'], $property['itr']);
                    $extracted[$name] = ['get' => $array_property, 'set' => $array_property];
                } else {
                    throw new MismatchedPropertiesException($property['get'], $property['set']);
                }
            } elseif ($hasGet) {
                if ($property['get']->getNumberOfParameters() === 0) {
                    // Regular get
                    $extracted[$name] = ['get' => $property['get']->name, 'set' => null];
                } elseif ($property['get']->getNumberOfParameters() === 1) {
                    // Array get
                    $extracted[$name] = ['get' => new ArrayProperty($property['get'], null, $property['itr']), 'set' => null];
                } else {
                    throw new InvalidPropertyException($property['get']);
                }
            } elseif ($hasSet) {
                if ($property['set']->getNumberOfParameters() === 1) {
                    // Regular set
                    $extracted[$name] = ['get' => null, 'set' => $property['set']->name];
                } elseif ($property['set']->getNumberOfParameters() === 2) {
                    // Array set
                    $extracted[$name] = ['get' => new ArrayProperty(null, $property['set'], $property['itr']), 'set' => null];
                } else {
                    throw new InvalidPropertyException($property['set']);
                }
            } else {
                $extracted[$name] = ['get' => new ArrayProperty(null, null, $property['itr']), 'set' => null];
            }
        }

        return $extracted;
    }
}
