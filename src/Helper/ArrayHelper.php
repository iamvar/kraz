<?php

declare(strict_types=1);

namespace Kraz\Helper;

class ArrayHelper
{
    public static function map(array $rows, string $keyField): array
    {
        $result = [];
        foreach ($rows as $row) {
            $key = $row[$keyField];
            $result[$key] = $row;
        }

        return $result;
    }
}