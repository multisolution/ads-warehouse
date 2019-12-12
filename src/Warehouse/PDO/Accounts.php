<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse\PDO;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Warehouse\Accounts as AccountsInterface;
use PDO;

class Accounts implements AccountsInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Account[]
     */
    public function __invoke(): array
    {
        $results = $this->pdo->query('select * from account')
            ->fetchAll(PDO::FETCH_CLASS, Account::class);

        if ($results === false) {
            return [];
        }

        /** @var Account[] */
        return $results;
    }
}
