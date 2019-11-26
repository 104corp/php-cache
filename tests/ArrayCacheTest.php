<?php

namespace Corp104\Cache;

use ArrayObject;
use Corp104\Cache\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use stdClass;
use Traversable;

class ArrayCacheTest extends TestCase
{
    /**
     * @var CacheInterface
     */
    protected $target;

    public function setUp()
    {
        $this->target = new ArrayCache();
    }

    public function tearDown()
    {
        $this->target = null;
    }

    /**
     * @test
     * @dataProvider getCases
     */
    public function shouldGetExceptedResult($key, $value)
    {
        // Arrange
        $excepted = $value;
        $this->target->set($key, $value);

        // Act
        $actual = $this->target->get($key);

        // Assert
        $this->assertEquals($excepted, $actual);
    }

    public function getCases()
    {
        return [
            ['key_1', 1],
            ['key_2', false],
            ['key_3', true],
            ['key_4', 'value'],
            ['key_5', ['item1', 'item2']],
            ['key_6', new stdClass],
        ];
    }

    /**
     * @test
     */
    public function shouldGetDefaultResult()
    {
        // Arrange
        $key = 'key';
        $default = 'default';
        $excepted = $default;
        $this->target->delete($key);

        // Act
        $actual = $this->target->get($key, $default);

        // Assert
        $this->assertSame($excepted, $actual);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidKeys
     */
    public function shouldThrowExceptionWhenCallSetWithInvalidKeys($invalidKey)
    {
        $this->target->set($invalidKey, 'invalid_keys');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidKeys
     */
    public function shouldThrowExceptionWhenCallHasWithInvalidKeys($invalidKey)
    {
        $this->target->has($invalidKey);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidKeys
     */
    public function shouldThrowExceptionWhenCallDeleteWithInvalidKeys($invalidKey)
    {
        $this->target->delete($invalidKey);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidKeys
     */
    public function shouldThrowExceptionWhenCallGetWithInvalidKeys($invalidKey)
    {
        $this->target->get($invalidKey);
    }

    public function getInvalidKeys()
    {
        return [
            [123],
            [123.123],
            [false],
            [true],
            [[123, 456]],
            [new stdClass],
        ];
    }

    /**
     * @test
     * @medium
     * @dataProvider getTTLs
     */
    public function shouldBeExpired($key, $ttl, $period)
    {
        // Arrange
        $excepted = null;
        $this->target->set($key, 'test_value', $ttl);
        usleep($period);

        // Act
        $actual = $this->target->get($key);

        // Assert
        $this->assertEquals($excepted, $actual);
    }

    public function getTTLs()
    {
        return [
            ['ttl_int_1', 1, 1100000],
            ['ttl_int_2', -1, 0],
            ['ttl_int_3', 0, 100000],
            ['ttl_date_interval', new \DateInterval('PT1S'), 1100000],
        ];
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenCallGetMultipleWithTraversable()
    {
        $this->target->getMultiple($this->getMockBuilder(Traversable::class)->getMock());
        $this->target->getMultiple(new ArrayObject());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionWhenCallGetMultipleWithNotTraversable()
    {
        $this->target->getMultiple('test');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionWhenCallDeleteMultipleWithNotTraversable()
    {
        $this->target->deleteMultiple('test');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionWhenCallSetMultipleWithNotTraversable()
    {
        $this->target->setMultiple('test');
    }

    /**
     * @test
     * @dataProvider getMultipleCase
     */
    public function shouldGetExceptedResultWhenCallGetMultiple($items)
    {
        // Arrange
        $excepted = $items;
        $keys = array_keys($excepted);
        $this->target->setMultiple($excepted);

        // Act
        $actual = $this->target->getMultiple($keys);

        // Assert
        $this->assertSame($excepted, $actual);
    }

    /**
     * @test
     * @dataProvider getMultipleCase
     */
    public function shouldReturnTrueWhenCallDeleteMultiple($items)
    {
        // Arrange
        $keys = array_keys($items);
        $this->target->setMultiple($items);

        // Act
        $actual = $this->target->deleteMultiple($keys);

        // Assert
        $this->assertTrue($actual);
    }

    public function getMultipleCase()
    {
        return [
            [
                [
                    'key1' => false,
                    'key2' => 123,
                    'key3' => 123.456,
                    'key4' => 'value3',
                    'key5' => ['value', 'value2'],
                    'key6' => new stdClass,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldReturnTrueAfterCallDelete()
    {
        // Arrange
        $key = 'key';
        $this->target->set($key, 'value');

        // Act
        $actual = $this->target->delete($key);

        // Assert
        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function shouldReturnTrueAfterCallClear()
    {
        // Arrange
        $this->target->set('key', 'value');

        // Act
        $actual = $this->target->clear();

        // Assert
        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function shouldBeExistWhenCallHas()
    {
        // Arrange
        $this->target->set('key', 'value');

        // Act
        $actual = $this->target->has('key');

        // Assert
        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function shouldBeNotExistWhenCallHas()
    {
        // Arrange
        $this->target->set('key', 'value');

        // Act
        $actual = $this->target->has('key1');

        // Assert
        $this->assertFalse($actual);
    }
}
