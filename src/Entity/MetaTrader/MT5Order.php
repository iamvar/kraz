<?php

declare(strict_types=1);

namespace Kraz\Entity\MetaTrader;

class MT5Order
{
    public const ORDER_STATE_STARTED = 0; // order started
    public const ORDER_STATE_PLACED = 1; // order placed in system
    public const ORDER_STATE_CANCELED = 2; // order canceled by client
    public const ORDER_STATE_PARTIAL = 3; // order partially filled
    public const ORDER_STATE_FILLED = 4; // order filled
    public const ORDER_STATE_REJECTED = 5; // order rejected
    public const ORDER_STATE_EXPIRED = 6; // order expired
    public const ORDER_STATE_REQUEST_ADD = 7; // order requested to add
    public const ORDER_STATE_REQUEST_MODIFY = 8; // order requested to modify
    public const ORDER_STATE_REQUEST_CANCEL = 9; // order requested to cancel

    public const OP_BUY = 0; // buy order
    public const OP_SELL = 1; // sell order
    public const OP_BUY_LIMIT = 2; // buy limit order
    public const OP_SELL_LIMIT = 3; // sell limit order
    public const OP_BUY_STOP = 4; // buy stop order
    public const OP_SELL_STOP = 5; // sell stop order
    public const OP_BUY_STOP_LIMIT = 6; // buy stop limit order
    public const OP_SELL_STOP_LIMIT = 7; // sell stop limit order
    public const OP_CLOSE_BY = 8; // close by
}