<?php

namespace Corp104\Cache\Util;

use Corp104\Cache\Exception\InvalidArgumentException;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;

class HelperTest extends TestCase
{
    /**
     * @test
     * @dataProvider getTtlCases
     */
    public function shouldGetExceptedResultWhenCallNormalizeTtl($ttl, $excepted)
    {
        $actual = Helper::normalizeTtl($ttl);

        $this->assertSame($excepted, $actual);
    }

    public function getTtlCases()
    {
        return [
            [null, null],
            [new DateInterval('PT1H'), 3600],
            [3600, 3600],
        ];
    }

    /**
     * @test
     */
    public function shouldGetExceptedResultWhenCallNormalizeExpireAtWithInt()
    {
        $ttl = 3600;
        $excepted = time() + $ttl;

        $actual = Helper::normalizeExpireAt($ttl);

        $this->assertSame($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldGetExceptedResultWhenCallNormalizeExpireAtWithDateInterval()
    {
        $ttl = new DateInterval('PT1H');
        $excepted = (new DateTimeImmutable)->add($ttl)->getTimestamp();

        $actual = Helper::normalizeExpireAt($ttl);

        $this->assertSame($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldGetExceptedResultWhenCallNormalizeExpireAtWithNull()
    {
        $ttl = $excepted = null;

        $actual = Helper::normalizeExpireAt($ttl);

        $this->assertSame($excepted, $actual);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidTtlCases
     */
    public function shouldThrowExceptionWhenCallNormalizeTtlWith($invalidTtl)
    {
        Helper::normalizeTtl($invalidTtl);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidTtlCases
     */
    public function shouldThrowExceptionWhenCallNormalizeExpireAtWith($invalidTtl)
    {
        Helper::normalizeExpireAt($invalidTtl);
    }

    public function getInvalidTtlCases()
    {
        return [
            ['string'],
            [1.23],
            [true],
            [[]],
            [new \stdClass],
        ];
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidKeys
     */
    public function shouldThrowExceptionWhenCheckStringTypeWith($invalidKey)
    {
        Helper::checkStringType($invalidKey);
    }

    public function getInvalidKeys()
    {
        return [
            [123],
            [123.123],
            [false],
            [true],
            [[123, 456]],
            [new \stdClass],
        ];
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenCheckTraversableTypeWithTraversableInput()
    {
        Helper::checkTraversableType($this->getMockBuilder(\Traversable::class)->getMock());
        Helper::checkTraversableType(new \ArrayObject());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionWhenCheckTraversableTypeWithNotTraversableInput()
    {
        Helper::checkTraversableType('test');
    }

    /**
     * @test
     * @dataProvider validTtlType
     */
    public function shouldBeOkayWhenCheckTtlTypeWith($validInput)
    {
        Helper::checkTtlType($validInput);
    }

    public function validTtlType()
    {
        return [
            [null],
            [3600],
            [new DateInterval('PT1H')],
        ];
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider invalidTtlType
     */
    public function shouldThrowExceptionWhenCheckTtlTypeWith($invalidInput)
    {
        Helper::checkTtlType($invalidInput);
    }

    public function invalidTtlType()
    {
        return [
            ['string'],
            [0.5],
            [false],
            [[]],
            [new stdClass()],
        ];
    }
}
