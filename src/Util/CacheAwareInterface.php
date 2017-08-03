<?php
namespace Corp104\Cache\Util;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Interface CacheAwareInterface is implemented by classes that depends on a Cache.
 */
interface CacheAwareInterface
{
    /**
     * Set the cache driver
     *
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache);

    /**
     * Set the cache TTL
     *
     * @param null|int|DateInterval $ttl Unit is Second
     */
    public function setTtl($ttl);
}
