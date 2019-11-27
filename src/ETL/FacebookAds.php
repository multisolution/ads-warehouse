<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;
use Ramsey\Uuid\Uuid;
use function AdsWarehouse\yesterday;

class FacebookAds extends ETL
{
    const SOURCE = 'Facebook';

    /** @var Api */
    private $api;
    /** @var string */
    private $adAccountId;

    public function __construct(Warehouse $warehouse, Api $api, string $adAccountId)
    {
        parent::__construct($warehouse);
        $this->api = $api;
        $this->adAccountId = $adAccountId;
    }

    protected function extract()
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
     * @param AdsInsights[] $data
     * @return Ad[]
     */
    protected function transform($data): array
    {
        return array_map(function (AdsInsights $insights): Ad {
            $data = $insights->getData();

            $ad = new Ad();
            $ad->id = Uuid::uuid4();
            $ad->name = $data['campaign_name'];
            $ad->cost = $data['spend'];
            $ad->impressions = $data['impressions'];
            $ad->clicks = $data['clicks'];
            $ad->cpm = $data['cpm'];
            $ad->cpc = $data['cpc'];
            $ad->ctr = $data['ctr'];
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
