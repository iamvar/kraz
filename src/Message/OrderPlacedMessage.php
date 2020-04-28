<?php

namespace Kraz\Message;

use Kraz\Service\IncoherentInterface;

/**
 * This event occurs when customer places a new order through trading terminal
 */
class OrderPlacedMessage implements IncoherentInterface
{
    private $platform;
    private $serverId;
    private $orderId;
    private $timestamp;
    private $login;
    private $isPending;
    private $volume;
    private $symbol;

    public function __construct(
        string $platform,
        int $serverId,
        int $orderId,
        int $timestamp,
        string $login,
        bool $isPending,
        float $volume,
        string $symbol
    ) {
        $this->platform = $platform;
        $this->serverId = $serverId;
        $this->orderId = $orderId;
        $this->timestamp = $timestamp;
        $this->login = $login;
        $this->isPending = $isPending;
        $this->volume = $volume;
        $this->symbol = $symbol;
    }

    public function getUniqueId(): string
    {
        return implode('-', [$this->getPlatform(), $this->getServerId(), $this->getOrderId()]);
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @return int
     */
    public function getServerId(): int
    {
        return $this->serverId;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->isPending;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }
}