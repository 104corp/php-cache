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
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        foreach ($keys as $key) {
            yield $key => $default;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return false;
    }
}
