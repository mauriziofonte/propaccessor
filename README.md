# PHP PropAccessor

A PHP Trait that adds support for direct access to object properties, with explicit accessors and mutators.

This package has been forked from [https://github.com/BapCat/Propifier](https://github.com/BapCat/Propifier) and updated to work with PHP 7.4 and support both _public_ and _protected_ accessors and mutators.
Original license (GNU GPL v3) has been kept. Credits to the original author.

## Installation

Installation can be done through Composer.

```bash
composer require mfonte/propaccessor
```

## Why use this package?

This class allows you to define properties in your classes that can be directly accessed and explicitly setted by accessing the object's properties. This is useful when you want to have control over the access to your object's properties, but you **want explicit and direct access to the properties themselves** without using _getters_ and _setters_.

## What does this package do?

This class allows you to define properties in your classes that can be **directly accessed** and **explicitly setted** by accessing the object's properties.

```php
class Foo {
    use \Mfonte\PropAccessor\PropifierTrait;

    private string $propertyOne;
    private int $properyTwo;
    private array $arrayableProperty = [];

    public function setPropertyOne(string $value) {
        $this->propertyOne = $value;
    }

    public function getPropertyOne() {
        return $this->propertyOne;
    }

    public function setPropertyTwo(int $value) {
        $this->propertyTwo = $value * 10;
    }

    public function getPropertyTwo() {
        return $this->propertyTwo;
    }

    public function setArrayableProperty(int $index, mixed $value) {
        $this->arrayableProperty[$index] = $value;
    }

    public function getArrayableProperty(int $index) {
        return $this->arrayableProperty[$index];
    }
}

$foo = new Foo();

$foo->propertyOne = 'I am Property One, and I\'ve been directly setted!';
echo $foo->propertyOne; // -> 'I am Property One, and I\'ve been directly setted!'

$foo->propertyTwo = 10;
echo $foo->propertyTwo; // -> 100 (setter multiplies by 10)

$foo->arrayableProperty[0] = 'I am the first element of the arrayable property!';
echo $foo->arrayableProperty[0]; // -> 'I am the first element of the arrayable property!'
```

This class also allows you to define **iterators** for your properties, so you can use them in `foreach` loops.

```php
class Foo {
    use \Mfonte\PropAccessor\PropifierTrait;

    private array $arrayableProperty = [];

    public function setArrayableProperty(int $index, mixed $value) {
        $this->arrayableProperty[$index] = $value;
    }

    public function getArrayableProperty(int $index) {
        return $this->arrayableProperty[$index];
    }

    public function itrArrayableProperty() {
        return new ArrayIterator($this->arrayableProperty);
    }
}

$foo = new Foo();

$foo->arrayableProperty[0] = 'I am the first element of the arrayable property!';
$foo->arrayableProperty[1] = 'I am the second element of the arrayable property!';
$foo->arrayableProperty[2] = 'I am the third element of the arrayable property!';

foreach($foo->arrayableProperty as $key => $value) {
    echo "Key: $key, Value: $value\n";
}

// Output:
// Key: 0, Value: I am the first element of the arrayable property!
// Key: 1, Value: I am the second element of the arrayable property!
// Key: 2, Value: I am the third element of the arrayable property!
```

