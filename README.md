# PHP PropAccessor

A PHP Trait that allows direct access to object properties via magic methods, mapping them to explicit accessor and mutator methods.

This package is a fork of [Propifier](https://github.com/BapCat/Propifier), updated for PHP 7.4 compatibility and extended functionality. Original license (GNU GPL v3) has been retained. Credits to the original author.

## Installation

Install via Composer:

```bash
composer require mfonte/propaccessor
```

## Why Use This Package?

In object-oriented programming, it's a common practice to use getter and setter methods to control access to an object's properties. However, this can lead to verbose code and reduce readability.

PHP PropAccessor allows you to:

* **Directly access properties** using `$object->property` syntax.
* **Maintain control** over property access through custom getter and setter methods.
* **Define array-like properties** that can be accessed using `$object->arrayProperty[$key]`.
* **Implement iterators** for properties, enabling `foreach` loops directly on object properties.

## How Does It Work?

By including the `PropifierTrait` in your class, the magic methods `__get()`, `__set()`, `__isset()`, and `__unset()` are implemented. These methods intercept property access and delegate to your defined getter, setter, and iterator methods based on naming conventions.

### Naming Conventions

* **Getter Methods**: `getPropertyName()`, `isPropertyName()`, or `hasPropertyName()`
* **Setter Methods**: `setPropertyName()`
* **Iterator Methods**: `itrPropertyName()`

### Property Name Resolution

The property name is derived from your method names by removing the prefix (`get`, `set`, `is`, `has`, `itr`) and converting the remainder to camelCase or snake\_case, depending on your preference.

## Examples

### Basic Usage

```php
use Mfonte\PropAccessor\PropifierTrait;

class User {
    use PropifierTrait;

    private string $name;

    public function setName(string $name): void {
        $this->name = ucfirst($name);
    }

    public function getName(): string {
        return $this->name;
    }
}

$user = new User();
$user->name = 'john';
echo $user->name; // Outputs 'John'
```

### Boolean Properties

```php
class FeatureToggle {
    use PropifierTrait;

    private bool $isEnabled = false;

    public function isEnabled(): bool {
        return $this->isEnabled;
    }

    public function setEnabled(bool $value): void {
        $this->isEnabled = $value;
    }
}

$feature = new FeatureToggle();
$feature->enabled = true;

if ($feature->enabled) {
    // Feature is enabled
}
```

### Array Properties

```php
use ArrayIterator;

class Collection {
    use PropifierTrait;

    private array $items = [];

    public function setItems(int $index, mixed $value): void {
        $this->items[$index] = $value;
    }

    public function getItems(int $index): mixed {
        return $this->items[$index] ?? null;
    }

    public function itrItems(): ArrayIterator {
        return new ArrayIterator($this->items);
    }
}

$collection = new Collection();
$collection->items[0] = 'Item 1';
$collection->items[1] = 'Item 2';

foreach ($collection->items as $index => $item) {
    echo "$index: $item\n";
}
```

### Custom Property Mappings

```php
class Config {
    use PropifierTrait;

    protected static array $propertyMap = [
        'dbHost' => ['get' => 'getDatabaseHost', 'set' => 'setDatabaseHost'],
    ];

    private string $databaseHost;

    protected function getDatabaseHost(): string {
        return $this->databaseHost;
    }

    protected function setDatabaseHost(string $host): void {
        $this->databaseHost = $host;
    }
}

$config = new Config();
$config->dbHost = 'localhost';
echo $config->dbHost; // Outputs 'localhost'
```

## Limitations and Notes

* **Virtual Properties**: The properties accessed are virtual and managed through magic methods.
* **Naming Conventions**: Method names must follow the defined naming conventions to be recognized.
* **No Automatic Type Conversion**: Ensure your getter and setter methods handle type conversions if necessary.
* **Custom Exceptions**: The package uses custom exceptions. Ensure they are included if you extract code snippets.

## License

This package is licensed under the GNU GPL v3. Credits to the original author of [Propifier](https://github.com/BapCat/Propifier).