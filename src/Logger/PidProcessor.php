<?php
declare(strict_types=1);

namespace Kraz\Logger;

class PidProcessor
{
    private $pid;

    public function __construct()
    {
        $this->pid = getmypid();
    }

    public function processRecord(array $record): array
    {
        if (!isset($record['extra']['pid'])) {
            $record['extra']['pid'] = $this->pid;
        }

        return $record;
    }
}
