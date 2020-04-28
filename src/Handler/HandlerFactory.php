<?php

declare(strict_types=1);

namespace Kraz\Handler;

use Kraz\Processor\OrderPlacedProcessor;
use Core\MessageBrokerClient\MessageHandlerInterface;
use InvalidArgumentException;

class HandlerFactory
{
    const MT4_ORDER = 'mt4_order';
    const MT5_ORDER = 'mt5_order';

    private $processor;

    public function __construct(OrderPlacedProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function getHandler(string $name): MessageHandlerInterface
    {
        switch ($name) {
            case self::MT4_ORDER: {
                return new Mt4OrderHandler($this->processor);
            }
            case self::MT5_ORDER: {
                return new Mt5OrderHandler($this->processor);
            }
        }
        throw new InvalidArgumentException("Unknown handler [$name]");
    }
}