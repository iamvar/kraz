<?php

namespace Kraz\Repository\CT;

use Doctrine\Common\Cache\CacheProvider;

class FollowerCachingRepository
{
    private const TIMEOUT = 3600;

    /**
     * @var FollowerRepository
     */
    private $repository;
    private $cache;

    public function __construct(FollowerRepository $repository, CacheProvider $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getAccountByLogin(string $login): ?array
    {
        $cacheKey = 'fcr-abl-' . $login;
        $account = $this->cache->fetch($cacheKey);
        if ($account === false) {
            $account = $this->repository->getAccountByLogin($login);
            $this->cache->save($cacheKey, $account, self::TIMEOUT);
        }

        return $account;
    }
}