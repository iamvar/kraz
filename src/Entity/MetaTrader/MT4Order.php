<?php

declare(strict_types=1);

namespace Kraz\Entity\MetaTrader;

class MT4Order
{
    // The `cmd` attribute values
    public const OP_BUY = 0;
    public const OP_SELL = 1;
    public const OP_BUY_LIMIT = 2;
    public const OP_SELL_LIMIT = 3;
    public const OP_BUY_STOP = 4;
    public const OP_SELL_STOP = 5;
    public const OP_BALANCE = 6;
    public const OP_CREDIT = 7;

    // The `state` attribute values
    public const TS_OPEN_NORMAL = 0;
    public const TS_OPEN_REMAND = 1;
    public const TS_OPEN_RESTORED = 2;
    public const TS_CLOSED_NORMAL = 3;
    public const TS_CLOSED_PART = 4;
    public const TS_CLOSED_BY = 5;
    public const TS_DELETED = 6;

    // The `reason` attribute values
    public const TR_REASON_CLIENT = 0; // client terminal
    public const TR_REASON_EXPERT = 1; // expert
    public const TR_REASON_DEALER = 2; // dealer
    public const TR_REASON_SIGNAL = 3; // signal
    public const TR_REASON_GATEWAY = 4; // gateway
    public const TR_REASON_MOBILE = 5; // mobile terminal
    public const TR_REASON_WEB = 6; // Web terminal
    public const TR_REASON_API = 7; // API
}