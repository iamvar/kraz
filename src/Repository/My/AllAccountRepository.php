<?php

declare(strict_types=1);

namespace Kraz\Repository\My;

use Kraz\Entity\My\Account;
use Kraz\Entity\My\DeAccount;
use Kraz\Service\ConnectionsConfiguratorService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use RuntimeException;

/**
 * Allows to retrieve account from both tables (li_account and de_account)
 */
class AllAccountRepository
{
    private $connectionsConfiguratorService;

    public function __construct(ConnectionsConfiguratorService $connectionsConfiguratorService)
    {
        $this->connectionsConfiguratorService = $connectionsConfiguratorService;
    }

    public function getConnection(): Connection
    {
        return $this->connectionsConfiguratorService->getConnectionToMy();
    }

    public function getAccountByLogin(string $login): ?array
    {
        $qb = $this->getConnection()->createQueryBuilder();

        $qbLi = $this->createAccountByLoginSubQuery(Account::TABLE_NAME, false);
        $qbDe = $this->createAccountByLoginSubQuery(DeAccount::TABLE_NAME, true);

        $qb
            ->select([
                '_t.client_id',
                '_t.account_type_id',
                '_t.currency_id',
                '_t.leverage',
                'is_de',
                'at.display_name AS account_type_display_name',
                'cl.company_id'
            ])
            ->from("({$qbLi->getSQL()} UNION ALL {$qbDe->getSQL()})", '_t')
            ->innerJoin('_t', 'client', 'cl', '_t.client_id = cl.id')
            ->innerJoin('_t', 'account_type', 'at', '_t.account_type_id = at.id')
            ->setParameter(':login', $login);

        $rows = $qb->execute()->fetchAll();

        if (count($rows) > 1) {
            throw new RuntimeException("Two records found for one login");
        }

        return $rows ? $rows[0] : null;
    }

    private function createAccountByLoginSubQuery(string $table, bool $isDe): QueryBuilder {
        $qb = $this->getConnection()->createQueryBuilder();
        $qb
            ->select('client_id, account_type_id, currency_id, leverage')
            ->addSelect((int)$isDe . ' AS is_de')
            ->from($table, $table)
            ->where('login = :login');

        return $qb;
    }
}