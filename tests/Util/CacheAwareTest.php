<?php
namespace Corp104\Cache\Util;

class CacheAwareTest extends \PHPUnit_Framework_TestCase
{
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
