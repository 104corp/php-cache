<?php
namespace Corp104\Cache;

use Corp104\Cache\Util\Helper;
use Psr\SimpleCache\CacheInterface;

/**
 * FAKE CACHE, JUST FOR TESTING
 */
class ArrayCache implements CacheInterface
{
    private $data = [];

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        Helper::checkStringType($key);

        if (!isset($this->data[$key])) {
            return $default;
        }

        $item = $this->data[$key];
        if ($item['expireAt'] !== null && time() >= $item['expireAt']) {
            return $default;
        }

        return $item['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        Helper::checkStringType($key);

        $this->data[$key] = [
            'value' => $value,
            'expireAt' => Helper::normalizeExpireAt($ttl)
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        Helper::checkStringType($key);

        unset($this->data[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        Helper::checkTraversableType($keys);

        $ret = [];
        foreach ($keys as $key) {
            $ret[$key] = $this->get($key, $default);
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        Helper::checkTraversableType($values);

        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        Helper::checkTraversableType($keys);

        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        Helper::checkStringType($key);

        return isset($this->data[$key]);
    }
}
