<?php

namespace Kraz\Service;

use Kraz\Entity\Source;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConnectionsConfiguratorService
{
    public const CONNECTION_MY = 'my_connection';
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function init(string $source)
    {
        switch ($source) {
            case Source::SOURCE_2: {
                $this->container->set(self::CONNECTION_MY, $this->container->get('doctrine.dbal.my_source2_connection'));
                return;
            }
            case Source::SOURCE_1: {
                $this->container->set(self::CONNECTION_MY, $this->container->get('doctrine.dbal.my_source1_connection'));
                return;
            }
        }

        throw new InvalidArgumentException("Unknown source [$source]");
    }

    public function getConnection(string $id): Connection
    {
        return $this->container->get($id);
    }

    /**
     * returns connection to my_source1 or my_source2 based on mapping
     * @return Connection
     */
    public function getConnectionToMy(): Connection
    {
        return $this->getConnection(self::CONNECTION_MY);
    }
}