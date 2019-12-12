<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse\PDO;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Load as LoadInterface;
use DateTime;
use PDO;

/**
 * @implements LoadInterface<Ad>
 */
class AdsLoad implements LoadInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param Ad[] $ads
     * @return mixed|void
     */
    public function __invoke(array $ads)
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
            $stmt->bindValue('impressions', $ad->impressions, PDO::PARAM_INT);
            $stmt->bindValue('clicks', $ad->clicks, PDO::PARAM_INT);
            $stmt->bindValue('cpm', $ad->cpm);
            $stmt->bindValue('cpc', $ad->cpc);
            $stmt->bindValue('ctr', $ad->ctr);
            $stmt->bindValue('source', $ad->source);
            $stmt->bindValue('date', $ad->date instanceof DateTime ? $ad->date->format('Y-m-d') : $ad->date); // Type-safety!!!

            $stmt->execute();
        }
    }
}
