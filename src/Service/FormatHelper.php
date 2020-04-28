<?php

declare(strict_types=1);

namespace Kraz\Service;

use DateTime;

class FormatHelper
{
    /**
     * return date in ISO8601 format
     */
    public static function getIsoDateFromTimestamp(int $timestamp): string
    {
        return date(DateTime::ATOM, $timestamp);
    }

}