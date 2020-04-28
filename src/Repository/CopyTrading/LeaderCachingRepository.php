<?php

namespace Kraz\Repository\CT;

use Kraz\Entity\CT\LeaderAccount;
use Doctrine\Common\Cache\CacheProvider;

class LeaderCachingRepository
{
    private const TIMEOUT = 3600;

    /**
     * @var LeaderRepository
     */
    private $repository;
    private $cache;

    public function __construct(LeaderRepository $repository, CacheProvider $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getAccountByLogin(string $login): ?LeaderAccount
    {
        $cacheKey = 'lcr-abl-' . $login;
        $account = $this->cache->fetch($cacheKey);
        if ($account === false) {
            $account = $this->repository->getAccountByLogin($login);
            $this->cache->save($cacheKey, $account, self::TIMEOUT);
        }

        return $account;
    }
}