<?php

declare(strict_types=1);

namespace Kraz\Service;

use Doctrine\Common\Cache\CacheProvider;

/**
 * The kafka topics for orders contains a some kind of replication log.
 * So, we need to try to convert this log into the real events bus.
 */
class DeduplicationService
{
    private CONST CACHE_KEY_PREFIX = 'dds';

    private $cache;

    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function isProcessed(IncoherentInterface $object): bool
    {
        return (bool)$this->cache->fetch($this->createCacheKey($object));
    }

    public function markAsProcessed(IncoherentInterface $object, int $timeout): void
    {
        $cacheKey = $this->createCacheKey($object);
        $this->cache->save($cacheKey, true, $timeout);
    }

    private function createCacheKey(IncoherentInterface $object): string
    {
        return self::CACHE_KEY_PREFIX . '-' . $object->getUniqueId();
    }
}