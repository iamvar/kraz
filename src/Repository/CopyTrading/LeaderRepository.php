<?php

declare(strict_types=1);

namespace Kraz\Repository\CT;

use Kraz\Entity\CT\LeaderAccount;
use Kraz\Helper\ArrayHelper;
use Doctrine\DBAL\Connection;

class LeaderRepository
{
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Returns some information about follower accounts.
     *
     * @param array $accountLogins
     *
     * @return array
     */
    public function getAccountsInfo(array $accountLogins): array
    {
        $qb = $this->conn->createQueryBuilder();

        $qb
            ->select('
                la.acc_no as account_login, 
                la.remun_fee,
                la.acc_name as acc_name,
                la.is_public,
                la.is_followable,
                leq.privacy_mode
            ')
            ->from('leader_accounts', 'la')
            ->leftJoin('la', 'leader_equity_stats', 'leq', 'leq.acc_no = la.acc_no')
            ->where('la.acc_no IN (:account_logins)')
            ->setParameter(':account_logins', $accountLogins, Connection::PARAM_INT_ARRAY);

        $rows = $qb->execute()->fetchAll();

        return ArrayHelper::map($rows, 'account_login');
    }

    public function getAccountByLogin(string $accountLogin): ?LeaderAccount
    {
        $info = $this->getAccountsInfo([$accountLogin]);
        if (empty($info)) {
            return null;
        }

        $info = $info[$accountLogin];
        return new LeaderAccount($info['account_login'], (bool)$info['is_public'], (bool)$info['is_followable']);
    }
}