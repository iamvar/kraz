<?php

declare(strict_types=1);

namespace Kraz\Repository\CT;

use Kraz\Helper\ArrayHelper;
use Doctrine\DBAL\Connection;

class FollowerRepository
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
            ->select('fa.acc_no as account_login, fa.lead_acc_no, la.remun_fee, la.acc_name')
            ->from('follower_accounts', 'fa')
            ->innerJoin('fa', 'leader_accounts', 'la', 'fa.lead_acc_no = la.acc_no')
            ->where('fa.acc_no IN (:account_logins)')
            ->setParameter(':account_logins', $accountLogins, Connection::PARAM_INT_ARRAY);

        $rows = $qb->execute()->fetchAll();

        return ArrayHelper::map($rows, 'account_login');
    }

    public function getAccountByLogin(string $accountLogin): ?array
    {
        $info = $this->getAccountsInfo([$accountLogin]);

        return $info[$accountLogin] ?? null;
    }
}