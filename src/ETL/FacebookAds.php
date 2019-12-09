<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use FacebookAds\Api;
use FacebookAds\Object\AbstractObject;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;
use Ramsey\Uuid\Uuid;
use function AdsWarehouse\yesterday;

/**
 * @template-extends ETL<array<int, AbstractObject>>
 */
class FacebookAds extends ETL
{
    private const SOURCE = 'Facebook';

    private Api $api;
    private int $adAccountId;

    public function __construct(Warehouse $warehouse, Api $api, int $adAccountId)
    {
        parent::__construct($warehouse);
        $this->api = $api;
        $this->adAccountId = $adAccountId;
    }

    /**
     * @return array<int, AbstractObject>
     */
    protected function extract(): array
    {
        $fields = [
            'impressions',
            'spend',
            'cpm',
            'ctr',
            'clicks',
            'cpc',
            'campaign_name',
        ];

        $params = [
            'level' => 'campaign',
            'date_preset' => AdsInsightsDatePresetValues::YESTERDAY,
        ];

        $ad_account = new AdAccount($this->adAccountId);
        return $ad_account->getInsights($fields, $params)->getArrayCopy();
    }

    /**
     * @param array<int, AdsInsights> $data
     * @return array<int, Ad>
     */
    protected function transform($data): array
    {
        return array_map(function (AdsInsights $insights): Ad {
            $data = $insights->getData();

            $ad = new Ad();
            $ad->id = Uuid::uuid4();
            $ad->name = $data['campaign_name'];
            $ad->cost = floatval($data['spend']);
            $ad->impressions = intval($data['impressions']);
            $ad->clicks = intval($data['clicks']);
            $ad->cpm = floatval($data['cpm']);
            $ad->cpc = floatval($data['cpc']);
            $ad->ctr = floatval($data['ctr']);
            $ad->source = $this->getSource();
            $ad->date = yesterday();

            return $ad;
        }, $data);
    }

    protected function getSource(): string
    {
        return self::SOURCE;
    }
}
