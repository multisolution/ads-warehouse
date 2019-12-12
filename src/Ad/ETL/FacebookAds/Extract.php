<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\ETL\FacebookAds;

use AdsWarehouse\Extract as ExtractInterface;
use FacebookAds\Api;
use FacebookAds\Cursor;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;

/**
 * @implements ExtractInterface<AdsInsights>
 */
class Extract implements ExtractInterface
{
    private Api $api;
    private string $adAccountId;

    public function __construct(Api $api, string $adAccountId)
    {
        $this->api = $api;
        $this->adAccountId = $adAccountId;
    }

    /**
     * @return AdsInsights[]
     */
    public function __invoke(): array
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

        /** @var Cursor $insights */
        $insights = $ad_account->getInsights($fields, $params);

        /** @var array<int, AdsInsights> */
        return $insights->getArrayCopy();
    }
}
