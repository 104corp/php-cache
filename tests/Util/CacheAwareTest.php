<?php

namespace Corp104\Cache\Util;

use Corp104\Cache\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CacheAwareTest extends TestCase
{
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @dataProvider getInvalidTtlCases
     */
    public function shouldThrowExceptionWhenGivenInvalidTTL($invalidTtl)
    {
        $target = $this->getMockForTrait(CacheAwareTrait::class);
        $target->setTtl($invalidTtl);
    }

    public function getInvalidTtlCases()
    {
        return [
            [false],
            [true],
            ['value'],
            [[]],
            [new \stdClass],
        ];
    }

    /**
     * @test
     * @dataProvider getValidTtlCases
     */
    public function shouldGetTtlWhenGivenValidTTL($excepted)
    {
        $target = $this->getMockForTrait(CacheAwareTrait::class);
        $target->setTtl($excepted);

        $actual = $target->getTtl();

        $this->assertSame($excepted, $actual);
    }

    public function getValidTtlCases()
    {
        return [
            [null],
            [86400],
            [new \DateInterval('PT1S')],
        ];
    }

    /**
     * @test
     */
    public function shouldGetDefaultTtlWhenDoNotSetTTL()
    {
        $excepted = 60;

        $target = $this->getMockForTrait(CacheAwareTrait::class);
        $target->expects($this->any())
            ->method('getDefaultTtl')
            ->will($this->returnValue($excepted));

        $actual = $target->getTtl();

        $this->assertSame($excepted, $actual);
    }
}
