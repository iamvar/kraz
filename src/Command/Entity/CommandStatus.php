<?php
declare(strict_types=1);

namespace Kraz\Command\Entity;

class CommandStatus
{
    public const OK = 0;
    public const GENERAL_ERROL = 1; // Catchall for general errors
    public const LOCK_FAILED = 3; // Lock acquiring failed
    public const API_CALL_FAILED = 4; // Failed to send request to Kraz API
}
