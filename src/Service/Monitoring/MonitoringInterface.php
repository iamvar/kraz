<?php
declare(strict_types=1);

namespace Kraz\Service\Monitoring;

interface MonitoringInterface
{
    public function info(string $message, array $params = []): MonitoringResponse;

    public function alert(string $message, array $params = []): MonitoringResponse;
}
