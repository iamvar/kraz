<?php
declare(strict_types=1);

namespace Kraz;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle,
            new \Symfony\Bundle\MonologBundle\MonologBundle,
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle,
        ];
    }

    /**
     * Load all services
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . "/../config/config_{$this->getEnvironment()}.yml");
    }

}