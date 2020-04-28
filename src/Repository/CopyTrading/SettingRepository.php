<?php

declare(strict_types=1);

namespace Kraz\Repository\CT;

use Doctrine\DBAL\Connection;

class SettingRepository
{
    private const INVESTOR_ACCOUNT_MIN_EQUITY_KEY = 'follower.min_equity';

    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function getInvestorMinDeposit(): ?float
    {
        $minDeposit = $this->getValue(self::INVESTOR_ACCOUNT_MIN_EQUITY_KEY);
        if ($minDeposit !== null) {
            return (float)$minDeposit;
        }

        return null;
    }

    private function getValue(string $parameter): ?string
    {
        $qb = $this->conn->createQueryBuilder();

        $qb
            ->select('ss.value')
            ->from('s_settings', 'ss')
            ->where("ss.setting = :parameter")
            ->setParameter(':parameter', $parameter);

        if ($investorMinDeposit = $qb->execute()->fetch()) {
            return (string)$investorMinDeposit['value'];
        }

        return null;
    }
}