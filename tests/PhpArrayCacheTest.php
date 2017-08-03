<?php
namespace Corp104\Cache;

use DateInterval;
use stdClass;

class PhpArrayCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $testCacheFile;

    protected function setUp()
    {
        $this->testCacheFile = __DIR__ . '/some-cache.php';
    }

    protected function tearDown()
    {
        @unlink($this->testCacheFile);
    }

    /**
     * @before
     */
    public function checkCacheFile()
    {
        try {
            FileCache::checkFileStatus($this->testCacheFile);
        } catch (\Exception $exception) {
            $this->markTestSkipped("Cache file '{$this->testCacheFile}' is not ready");
        }
    }

    /**
     * @test
     */
    public function shouldGetNullWhenCallGetWithDoNothingWithoutCache()
    {
        $key = 'some-key';
        $target = new FileCache($this->testCacheFile);

        $actual = $target->get($key);

        $this->assertNull($actual);
    }

    /**
     * @test
     */
    public function shouldGetNullWhenCallGetWithDefaultDataWithoutCache()
    {
        $key = 'some-key';
        $excepted = 'some-default-value';
        $target = new FileCache($this->testCacheFile);

        $actual = $target->get($key, $excepted);

        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     * @dataProvider getCases
     */
    public function shouldBeOkWhenCallSetWithData($excepted)
    {
        $key = 'some-key';
        $target = new FileCache($this->testCacheFile);
        $target->set($key, $excepted);

        $actual = $target->get($key);

        $this->assertEquals($excepted, $actual);
    }

    public function getCases()
    {
        return [
            [1],
            [3.14],
            [false],
            [true],
            [null],
            ['value'],
            ['with-double-quote-in-\'here\''],
            ['with-single-quote-in-"here"'],
            ['with-slash-in-\\here\\'],
            ['with-space-in- here '],
            [['item1', 'item2']],
            [new stdClass()],
        ];
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallSetWithMultiData()
    {
        $key1 = 'some-key1';
        $exceptedData1 = 'some-data1';
        $key2 = 'some-key2';
        $exceptedData2 = 'some-data2';

        $target = new FileCache($this->testCacheFile);
        $target->set($key1, $exceptedData1);
        $target->set($key2, $exceptedData2);

        $actualData1 = $target->get($key1);
        $actualData2 = $target->get($key2);

        $this->assertEquals($exceptedData1, $actualData1);
        $this->assertEquals($exceptedData2, $actualData2);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallDeleteWithExistData()
    {
        $key = 'some-key';
        $notExcepted = 'some-data';
        $target = new FileCache($this->testCacheFile);
        $target->set($key, $notExcepted);

        $target->delete($key);
        $actual = $target->get($key);

        $this->assertNotEquals($notExcepted, $actual);
        $this->assertNull($actual);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallDeleteWithNotExistData()
    {
        $target = new FileCache($this->testCacheFile);
        $actual = $target->delete('not-exist-key');

        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallGetMultipleWithMultiData()
    {
        $key1 = 'some-key1';
        $data1 = 'some-data1';
        $key2 = 'some-key2';
        $data2 = 'some-data2';

        $excepted = [
            $key1 => $data1,
            $key2 => $data2,
        ];

        $target = new FileCache($this->testCacheFile);
        $target->set($key1, $data1);
        $target->set($key2, $data2);

        $actual = $target->getMultiple([$key1, $key2]);

        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallSetMultipleWithMultiData()
    {
        $key1 = 'some-key1';
        $data1 = 'some-data1';
        $key2 = 'some-key2';
        $data2 = 'some-data2';

        $excepted = [
            $key1 => $data1,
            $key2 => $data2,
        ];

        $target = new FileCache($this->testCacheFile);
        $target->setMultiple($excepted);

        $actual = $target->getMultiple([$key1, $key2]);

        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallGetMultipleWithMultiDataHaveEmptyData()
    {
        $key1 = 'some-key';
        $data1 = 'some-data';
        $key2 = 'no-data-key';

        $excepted = [
            $key1 => $data1,
            $key2 => null,
        ];

        $target = new FileCache($this->testCacheFile);
        $target->set($key1, $data1);

        $actual = $target->getMultiple([$key1, $key2]);

        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallHasWithDataAndDataNotExist()
    {
        $key = 'some-key';
        $data = 'some-data';

        $target = new FileCache($this->testCacheFile);
        $target->set($key, $data);

        $this->assertTrue($target->has($key));
        $this->assertFalse($target->has('not-exist-key'));
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallDeleteMultipleWithMultiData()
    {
        $key1 = 'some-key1';
        $data1 = 'some-data1';
        $key2 = 'some-key2';
        $data2 = 'some-data2';

        $exceptedData = [
            $key1 => $data1,
            $key2 => null,
        ];

        $target = new FileCache($this->testCacheFile);
        $target->set($key1, $data1);
        $target->set($key2, $data2);

        $actualResult = $target->deleteMultiple([$key2, 'not-exist-key']);
        $actualData = $target->getMultiple([$key1, $key2]);

        $this->assertTrue($actualResult);
        $this->assertEquals($exceptedData, $actualData);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallClearWithMultiData()
    {
        $key1 = 'some-key1';
        $data1 = 'some-data1';
        $key2 = 'some-key2';
        $data2 = 'some-data2';

        $exceptedData = [
            $key1 => null,
            $key2 => null,
        ];

        $target = new FileCache($this->testCacheFile);
        $target->set($key1, $data1);
        $target->set($key2, $data2);

        $actualResult = $target->clear();
        $actualData = $target->getMultiple([$key1, $key2]);

        $this->assertTrue($actualResult);
        $this->assertEquals($exceptedData, $actualData);
    }

    /**
     * @test
     * @medium
     * @dataProvider getTTLs
     */
    public function shouldBeExpired($key, $ttl, $period)
    {
        $excepted = null;

        $target = new FileCache($this->testCacheFile);
        $target->set($key, 'test_value', $ttl);
        usleep($period);

        $actual = $target->get($key);

        $this->assertEquals($excepted, $actual);
    }

    public function getTTLs()
    {
        return [
            ['ttl_int_1', 1, 1100000],
            ['ttl_int_2', -1, 0],
            ['ttl_int_3', 0, 100000],
            ['ttl_date_interval', new DateInterval('PT1S'), 1100000],
        ];
    }

    /**
     * @test
     */
    public function shouldReturnValueFirstSetWhenAnotherProcessGetTheCacheFile()
    {
        $key = 'some-key';
        $excepted = 'some-default-value';

        $target1 = new FileCache($this->testCacheFile);
        $target1->set($key, $excepted);

        $target2 = new FileCache($this->testCacheFile);
        $actual = $target2->get($key);

        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldReturnValueFirstSetWhenAnotherProcessGetTheCacheFileWithObjectValue()
    {
        $key = 'some-key';
        $excepted = new stdClass();
        $excepted->someObj = new stdClass();

        $target1 = new FileCache($this->testCacheFile);
        $target1->set($key, $excepted);

        $target2 = new FileCache($this->testCacheFile);
        $actual = $target2->get($key);

        $this->assertEquals($excepted, $actual);
    }

    /**
     * @test
     */
    public function shouldBeOkWhenCallSetWithComplexData()
    {
        $obj = new stdClass();
        $obj->someProperty = new stdClass();
        $obj->someArray = ['some-value'];
        $obj->someInt = 123;
        $obj->someFloat = 3.14;
        $obj->someString = 'some-string';

        $arrayObjectData = 'some-value';

        $arrayCache = new ArrayCache();
        $arrayCache->set('some-key', $arrayObjectData);

        $excepted = [
            'some-str',
            'obj' => $obj,
            'fakeCache' => $arrayCache,
        ];

        $key = 'some-key';
        $target = new FileCache($this->testCacheFile);
        $target->set($key, $excepted);

        $actual = $target->get($key);

        $this->assertEquals($excepted, $actual);
        $this->assertEquals($excepted['obj']->someInt, $actual['obj']->someInt);
        $this->assertEquals($arrayObjectData, $actual['fakeCache']->get('some-key'));
    }
}
