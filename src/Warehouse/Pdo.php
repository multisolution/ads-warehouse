<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Ad\Ad;
use DateTime;

class Pdo implements Warehouse
{
    /** @var \PDO */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param Ad[] $ads
     * @return bool
     */
    public function store(array $ads): void
    {
        $stmt = $this->pdo->prepare(
            'insert into ad (id, name, cost, impressions, clicks, cpm, cpc, ctr, source, date) values (:id, :name, :cost, :impressions, :clicks, :cpm, :cpc, :ctr, :source, :date)'
        );

        foreach ($ads as $ad) {
            $stmt->bindValue('id', $ad->id);
            $stmt->bindValue('name', $ad->name);
            $stmt->bindValue('cost', $ad->cost);
            $stmt->bindValue('impressions', $ad->impressions, \PDO::PARAM_INT);
            $stmt->bindValue('clicks', $ad->clicks, \PDO::PARAM_INT);
            $stmt->bindValue('cpm', $ad->cpm);
            $stmt->bindValue('cpc', $ad->cpc);
            $stmt->bindValue('ctr', $ad->ctr);
            $stmt->bindValue('source', $ad->source);
            $stmt->bindValue('date', $ad->date->format('Y-m-d'));

            $stmt->execute();
        }
    }

    /**
     * @return Ad[]
     */
    public function items(): array
    {
        return $this->pdo->query('select * from ad order by timestamp desc')
            ->fetchAll(\PDO::FETCH_CLASS, Ad::class);
    }

    public function drop(string $source, DateTime $date): void
    {
        $this->pdo->prepare('delete from ad where source = ? and date = ?')->execute([$source, $date->format('Y-m-d')]);
    }
}
