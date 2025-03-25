<?php

declare(strict_types=1);

use Mfonte\PropAccessor\Exception\InvalidPropertyException;
use Mfonte\PropAccessor\Exception\MismatchedPropertiesException;
use Mfonte\PropAccessor\Exception\NoSuchPropertyException;
use PHPUnit\Framework\TestCase;

class PropifierTraitTest extends TestCase
{
    /**
     * Set up the test environment by including all stub classes.
     */
    public function setUp(): void
    {
        foreach (glob(__DIR__ . '/stubs/*.php') as $file) {
            require_once $file;
        }
    }

    /**
     * Test basic getter and setter functionality.
     */
    public function testMagicProperties(): void
    {
        $value = new Dummy();
        $value->something = 'test';
        $value->another = 'test2';
        $value->calculator = 10;

        $this->assertSame('test', $value->something);
        $this->assertSame('test2', $value->another);
        $this->assertSame(100, $value->calculator); // Value multiplied by 10 in setter
    }

    /**
     * Test array-like property access and iteration.
     */
    public function testArrayProperties(): void
    {
        $value = new Dummy();
        $value->array[0] = 100;
        $this->assertSame(100, $value->array[0]);

        // Test iteration over array property
        $value->array[1] = 200;
        $expected = [100, 200];
        $result = [];
        foreach ($value->array as $item) {
            $result[] = $item;
        }
        $this->assertSame($expected, $result);
    }

    /**
     * Test accessing a property that does not exist.
     */
    public function testGetDoesntExist(): void
    {
        $value = new Dummy();

        $this->expectException(NoSuchPropertyException::class);
        $value->nonExistentProperty;
    }

    /**
     * Test setting a property that does not exist.
     */
    public function testSetDoesntExist(): void
    {
        $value = new Dummy();

        $this->expectException(NoSuchPropertyException::class);
        $value->nonExistentProperty = 'value';
    }

    /**
     * Test a class with a getter only.
     */
    public function testGetOnly(): void
    {
        $value = new GetOnly();
        $this->assertSame('test', $value->something);

        $this->expectException(NoSuchPropertyException::class);
        $value->something = 'new value';
    }

    /**
     * Test a class with a setter only.
     */
    public function testSetOnly(): void
    {
        $value = new SetOnly();
        $value->something = 'new value';

        $this->expectException(NoSuchPropertyException::class);
        $value->something;
    }

    /**
     * Test array property with getter only.
     */
    public function testArrayGetOnly(): void
    {
        $value = new ArrayGetOnly();
        $this->assertSame('value', $value->arrayProperty['key']);

        $this->expectException(NoSuchPropertyException::class);
        $value->arrayProperty['key'] = 'new value';
    }

    /**
     * Test array property with setter only.
     */
    public function testArraySetOnly(): void
    {
        $value = new ArraySetOnly();
        $value->arrayProperty['key'] = 'value';

        $this->expectException(NoSuchPropertyException::class);
        $value->arrayProperty['key'];
    }

    /**
     * Test mismatched properties where getter and setter have incompatible signatures.
     */
    public function testMismatchedProperties(): void
    {
        $value = new Mismatch();

        $this->expectException(MismatchedPropertiesException::class);
        $value->something;
    }

    /**
     * Test invalid getter method with too many parameters.
     */
    public function testInvalidGetter(): void
    {
        $value = new InvalidGet();

        $this->expectException(InvalidPropertyException::class);
        $value->something;
    }

    /**
     * Test invalid setter method with too many parameters.
     */
    public function testInvalidSetter(): void
    {
        $value = new InvalidSet();

        $this->expectException(InvalidPropertyException::class);
        $value->something = 'value';
    }

    /**
     * Test boolean properties using 'is' and 'has' methods.
     */
    public function testBooleanProperties(): void
    {
        $value = new BooleanProperties();
        $this->assertFalse($value->active);

        $value->active = true;
        $this->assertTrue($value->active);

        $this->assertTrue($value->hasItems);
    }

    /**
     * Test custom property mappings.
     */
    public function testCustomPropertyMappings(): void
    {
        $value = new CustomMapping();
        $value->customProperty = 'custom value';

        $this->assertSame('custom value', $value->customProperty);
    }

    /**
     * Test the __unset() magic method.
     */
    public function testUnsetProperty(): void
    {
        $value = new UnsettableProperty();
        $value->property = 'value';
        $this->assertSame('value', $value->property);

        unset($value->property);

        $this->expectException(NoSuchPropertyException::class);
        $value->property;
    }

    /**
     * Test isset() and empty() magic methods.
     */
    public function testIssetAndEmpty(): void
    {
        $value = new GetOnly();
        $this->assertTrue(isset($value->something));
        $this->assertFalse(empty($value->something));

        $this->assertFalse(isset($value->nonExistentProperty));
        $this->assertTrue(empty($value->nonExistentProperty));
    }

    /**
     * Test accessing properties that are not mapped.
     */
    public function testUnmappedProperties(): void
    {
        $value = new UnmappedProperties();

        $this->expectException(NoSuchPropertyException::class);
        $value->unmappedProperty;
    }

    /**
     * Test exception handling when trying to iterate over a non-iterable property.
     */
    public function testInvalidIteration(): void
    {
        $value = new Dummy();

        $this->expectException(NoSuchPropertyException::class);
        foreach ($value->something as $item) {
            // Should not reach here
        }
    }

    /**
     * Test array property with iterator only.
     */
    public function testArrayIteratorOnly(): void
    {
        $in = ['a' => 'b'];
        $value = new ArrayItrOnly($in);

        $out = [];
        foreach ($value->arr as $key => $val) {
            $out[$key] = $val;
        }

        $this->assertSame($in, $out);

        $this->expectException(NoSuchPropertyException::class);
        $value->arr['key'];
    }

    /**
     * Test combined array property with getter, setter, and iterator.
     */
    public function testArrayAll(): void
    {
        $value = new ArrayAll();
        $value->arr['key'] = 'value';

        $this->assertSame('value', $value->arr['key']);

        $expected = ['key' => 'value'];
        $result = [];
        foreach ($value->arr as $key => $val) {
            $result[$key] = $val;
        }
        $this->assertSame($expected, $result);
    }

    /**
     * Test property existence with __isset().
     */
    public function testPropertyIsset(): void
    {
        $value = new GetOnly();
        $this->assertTrue(isset($value->something));
        $this->assertFalse(isset($value->nonExistentProperty));
    }

    /**
     * Test unsetting a property that does not have an unset method.
     */
    public function testUnsetNonUnsettableProperty(): void
    {
        $value = new Dummy();

        $this->expectException(NoSuchPropertyException::class);
        unset($value->something);
    }

    /**
     * Test that accessing a property with a private or non-accessible method results in an exception.
     */
    public function testNonAccessibleMethods(): void
    {
        $value = new NonAccessibleMethods();

        $this->expectException(NoSuchPropertyException::class);
        $value->property;
    }

    /**
     * Test accessing a property with custom method names.
     */
    public function testCustomMethodNames(): void
    {
        $value = new CustomMethodNames();
        $value->customProperty = 'value';

        $this->assertSame('value', $value->customProperty);
    }
}
