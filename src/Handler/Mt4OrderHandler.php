<?php

declare(strict_types=1);

namespace Kraz\Handler;

use Kraz\Entity\MetaTrader\MT4Order;
use Kraz\Message\OrderPlacedMessage;
use Kraz\Processor\OrderPlacedProcessor;
use Core\MessageBrokerClient\MessageHandlerInterface;
use Core\MessageBrokerClient\MessageInterface;
use Core\MessageBrokerClient\MessageParseException;

/**
 * Initially created to process messages from trunk_mt4_record topic
 */
class Mt4OrderHandler implements MessageHandlerInterface
{
    private const INSTANT_ORDER_COMMANDS = [
        MT4Order::OP_BUY,
        MT4Order::OP_SELL,
    ];
    private const PENDING_ORDER_COMMANDS = [
        MT4Order::OP_BUY_LIMIT,
        MT4Order::OP_SELL_LIMIT,
        MT4Order::OP_BUY_STOP,
        MT4Order::OP_SELL_STOP,
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

            $this->next->process($this->createOrderMessage($data));
        } catch (MessageParseException $e) {
            echo 'Invalid message received: '.$e->getMessage();
        }
    }

    private function shouldBeSkipped(array $data): bool
    {
        if (!in_array($data['cmd'], array_merge(self::INSTANT_ORDER_COMMANDS, self::PENDING_ORDER_COMMANDS))) {
            // This  row is not related with order opening
            return true;
        }

        if ($data['reason'] != MT4Order::TR_REASON_CLIENT) {
            // We process only orders from the client
            return true;
        }

        if ($data['state'] != MT4Order::TS_OPEN_NORMAL) {
            // We process only records when order is opened normally
            return true;
        }

        return false;
    }

    private function createOrderMessage(array $data): OrderPlacedMessage
    {
        $platform = 'mt4';
        $serverId = (int)$data['frs_ServerID'];
        $orderId = (int)$data['order'];
        $timestamp = (int)$data['timestamp'];
        $login = (string)$data['login'];
        $isPending = (bool)in_array($data['cmd'], self::PENDING_ORDER_COMMANDS);
        // The MT4 stores order volume in the lots multiplied by 100
        $volume = (float)($data['volume'] / 100);
        $symbol = (string)$data['symbol'];
        return new OrderPlacedMessage($platform, $serverId, $orderId, $timestamp, $login, $isPending, $volume, $symbol);
    }
}