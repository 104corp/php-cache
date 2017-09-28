<?php
namespace Corp104\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * DUMMY CACHE, JUST FOR TESTING OR DISABLE CACHE IF NEEDED
 *
 * @see https://github.com/symfony/cache/blob/master/Simple/NullCache.php
 */
class DummyCache implements CacheInterface
{
    public function get($key, $default = null)
    {
        return $default;
    }

    public function set($key, $value, $ttl = null)
    {
        return false;
    }

    public function delete($key)
    {
        return true;
    }

    public function clear()
    {
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        foreach ($keys as $key) {
            yield $key => $default;
        }
    }

    public function setMultiple($values, $ttl = null)
    {
        return false;
    }

    public function deleteMultiple($keys)
    {
        return true;
    }

    public function has($key)
    {
        return false;
    }
}
