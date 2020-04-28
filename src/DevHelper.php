<?php

namespace Kraz;

class DevHelper
{
    public static function printQuery(\Doctrine\DBAL\Query\QueryBuilder $q) {

        $sql = $q->getSQL();
        $params = $q->getParameters();

        foreach ($params as $key => $value) {
            if ($value instanceof \DateTimeImmutable) {
                $value = $value->format('\'Y-m-d H:i:s\'');
            } elseif (is_array($value)) {
                $value = implode(',', $value);
            } else {
                $value = "'$value'";
            }

            $sql = str_replace($key, $value, $sql);
        }

        print_r($sql);
    }
}