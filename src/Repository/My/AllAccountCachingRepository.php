<?php

namespace Kraz\Repository\My;

use Doctrine\Common\Cache\CacheProvider;

class AllAccountCachingRepository
{
    private const TIMEOUT = 3600; // Cache account data for 1 hour

    /**
     * @var AllAccountRepository
     */
    private $repository;
    private $cache;

    public function __construct(AllAccountRepository $repository, CacheProvider $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getAccountByLogin(string $login): ?array
    {
        $cacheKey = 'acr-abl-'.$login;
        $account = $this->cache->fetch($cacheKey);
        if ($account === false) {
            $account = $this->repository->getAccountByLogin($login);
            $this->cache->save($cacheKey, $account, self::TIMEOUT);
        }

        return $account;
    }
}