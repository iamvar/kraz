<?php
declare(strict_types=1);

namespace Kraz\Service;

use Doctrine\Common\Cache\RedisCache;
use Redis;

class RedisService extends RedisCache
{
    public function __construct(string $host, int $port)
    {
        $redis = new Redis();
        $redis->connect($host, $port);
        $this->setRedis($redis);
    }
}