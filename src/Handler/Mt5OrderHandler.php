<?php

declare(strict_types=1);

namespace Kraz\Handler;

use Kraz\Entity\MetaTrader\MT5Order;
use Kraz\Message\OrderPlacedMessage;
use Kraz\Processor\OrderPlacedProcessor;
use Core\MessageBrokerClient\MessageHandlerInterface;
use Core\MessageBrokerClient\MessageInterface;
use Core\MessageBrokerClient\MessageParseException;

/**
 * Initially created to process messages from trunk_mt5_order topic
 */
class Mt5OrderHandler implements MessageHandlerInterface
{
    private const INSTANT_ORDER_TYPE = [
        MT5Order::OP_BUY,
        MT5Order::OP_SELL,
    ];

    private const PENDING_ORDER_TYPE = [
        MT5Order::OP_BUY_LIMIT,
        MT5Order::OP_SELL_LIMIT,
        MT5Order::OP_BUY_STOP,
        MT5Order::OP_SELL_STOP,
        MT5Order::OP_BUY_STOP_LIMIT,
        MT5Order::OP_SELL_STOP_LIMIT
    ];

    private $next;

    public function __construct(OrderPlacedProcessor $next)
    {
        $this->next = $next;
    }

    public function handle(MessageInterface $message)
    {
        try {
            $data = $message->getContent();
            if ($this->shouldBeSkipped($data)) {
                // We have to skip this event
                return;
            }

            $this->next->process($this->createOrderPlacedMessage($data));
        } catch (MessageParseException $e) {
            echo 'Invalid message received: '.$e->getMessage();
        }
    }

    private function shouldBeSkipped(array $data): bool
    {
        if ($data['frs_RecOperation'] == 'D') {
            // We are not processing the rows deletion
            return true;
        }

        if (!in_array($data['Type'], array_merge(self::INSTANT_ORDER_TYPE, self::PENDING_ORDER_TYPE))) {
            // This row is not related with unknown order type
            return true;
        }

        if ($data['State'] != MT5Order::ORDER_STATE_PLACED) {
            // Now we process only order placed events
            return true;
        }

        if ($data['PositionID'] > 0) {
            // That is an order to close the position. We have to skip it.
            return true;
        }

        return false;
    }

    private function createOrderPlacedMessage(array $data): OrderPlacedMessage
    {
        $platform = 'mt5';
        $serverId = (int)$data['frs_ServerID'];
        $orderId = (int)$data['Order'];
        $timestamp = (int) ($data['TimeSetup']);
        $login = (string)$data['Login'];
        $isPending = (bool)in_array($data['Type'], self::PENDING_ORDER_TYPE);
        // The MT5 stores order volume in the lots multiplied by 10000
        $volume = (float)($data['VolumeCurrent'] / 10000);
        $symbol = (string)$data['Symbol'];
        return new OrderPlacedMessage($platform, $serverId, $orderId, $timestamp, $login, $isPending, $volume, $symbol);
    }
}