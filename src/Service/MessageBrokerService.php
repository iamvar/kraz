<?php

declare(strict_types=1);

namespace Kraz\Service;

use Core\MessageBrokerClient\ConsumerFactory;
use Core\MessageBrokerClient\ConsumerInterface;
use Core\MessageBrokerClient\MessageBrokerClientException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class MessageBrokerService
{
    private $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $cluster
     *
     * @throws MessageBrokerClientException
     *
     * @return ConsumerInterface
     */
    public function getConsumer(string $cluster): ConsumerInterface
    {
        $config = $this->getClusterConfig($cluster);

        return ConsumerFactory::getConsumer($config);
    }

    private function getClusterConfig(string $cluster): array
    {
        $cfg = $this->config[$cluster] ?? false;
        if ($cfg === null) {
            throw new InvalidConfigurationException("Cluster [$cluster] is not configured");
        }

        return $cfg;
    }
}