<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\ETL\FacebookAds;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Transform as TransformInterface;
use FacebookAds\Object\AdsInsights;
use Ramsey\Uuid\Uuid;
use function AdsWarehouse\yesterday;

/**
 * @implements TransformInterface<AdsInsights, Ad>
 */
class Transform implements TransformInterface
{
    private Account $account;
    private string $source;

    public function __construct(Account $account, string $source)
    {
        $this->account = $account;
        $this->source = $source;
    }

    /**
     * @param AdsInsights[] $data
     * @return Ad[]
     */
    public function __invoke(array $data): array
    {
        return array_map(function (AdsInsights $insights): Ad {
            /** @var array<string, string> $data */
            $data = $insights->getData();

            $ad = new Ad();
            $ad->id = Uuid::uuid4();
            $ad->account = $this->account;
            $ad->name = $data['campaign_name'];
            $ad->cost = floatval($data['spend']);
            $ad->impressions = intval($data['impressions']);
            $ad->clicks = intval($data['clicks']);
            $ad->cpm = floatval($data['cpm']);
            $ad->cpc = floatval($data['cpc']);
            $ad->ctr = floatval($data['ctr']);
            $ad->source = $this->source;
            $ad->date = yesterday();

            return $ad;
        }, $data);
    }
}
