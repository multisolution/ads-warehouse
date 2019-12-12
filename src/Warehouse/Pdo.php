<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Ad\Ad;
use DateTime;

class Pdo implements Warehouse
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @param array<int, Ad> $ads */
    public function store(array $ads): void
    {
        $stmt = $this->pdo->prepare(
            'insert into ad (id, account_id, name, cost, impressions, clicks, cpm, cpc, ctr, source, date)
            values (:id, :account_id, :name, :cost, :impressions, :clicks, :cpm, :cpc, :ctr, :source, :date)'
        );

        foreach ($ads as $ad) {
            $stmt->bindValue('id', $ad->id);
            $stmt->bindValue('account_id', $ad->account->id);
            $stmt->bindValue('name', $ad->name);
            $stmt->bindValue('cost', $ad->cost);
            $stmt->bindValue('impressions', $ad->impressions, \PDO::PARAM_INT);
            $stmt->bindValue('clicks', $ad->clicks, \PDO::PARAM_INT);
            $stmt->bindValue('cpm', $ad->cpm);
            $stmt->bindValue('cpc', $ad->cpc);
            $stmt->bindValue('ctr', $ad->ctr);
            $stmt->bindValue('source', $ad->source);
            $stmt->bindValue('date', $ad->date instanceof DateTime ? $ad->date->format('Y-m-d') : $ad->date); // Type-safety!!!

            $stmt->execute();
        }
    }

    /**
     * @return array<int, Ad>
     */
    public function items(): array
    {
        /** @var array<int, Ad>|false $results */
        $results = $this->pdo->query('select * from ad order by timestamp desc')
            ->fetchAll(\PDO::FETCH_CLASS, Ad::class);

        if ($results === false) {
            return [];
        }

        return $results;
    }

    public function drop(string $source, DateTime $date): void
    {
        $this->pdo->prepare('delete from ad where source = ? and date = ?')->execute([$source, $date->format('Y-m-d')]);
    }

    /**
     * @return array<int, Account>
     */
    public function accounts(): array
    {
        /** @var array<int, Account>|false $results */
        $results = $this->pdo->query('select * from account')
            ->fetchAll(\PDO::FETCH_CLASS, Account::class);

        if ($results === false) {
            return [];
        }

        return $results;
    }
}
