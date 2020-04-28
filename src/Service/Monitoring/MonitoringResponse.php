<?php
declare(strict_types=1);

namespace Kraz\Service\Monitoring;

class MonitoringResponse
{
    private $error;

    public function __construct(?string $error = null)
    {
        $this->error = (string)$error;
    }

    public function hasError(): bool
    {
        return (bool)$this->error;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
