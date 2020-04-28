<?php
declare(strict_types=1);

namespace Kraz\Service\Monitoring;

class MonitoringMock implements MonitoringInterface
{
    public function info(string $message, array $performance = []): MonitoringResponse
    {
        return new MonitoringResponse();
    }

    public function alert(string $message, array $performance = []): MonitoringResponse
    {
        return new MonitoringResponse();
    }
}
