<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use FacebookAds\Api;
use FacebookAds\Cursor;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Object\Values\AdsInsightsDatePresetValues;
use Ramsey\Uuid\Uuid;
use function AdsWarehouse\yesterday;

/**
 * @template-extends ETL<array<int, AdsInsights>>
 */
class FacebookAds extends ETL
{
    private const SOURCE = 'Facebook';

    private Api $api;

    public function __construct(Warehouse $warehouse, Account $account, Api $api)
    {
        parent::__construct($warehouse, $account);
        $this->api = $api;
    }

    /**
     * @return array<int, AdsInsights>
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

        $ad_account = new AdAccount(strval($this->account->fbAdAccountId));

        /** @var Cursor $insights */
        $insights = $ad_account->getInsights($fields, $params);

        /** @var array<int, AdsInsights> */
        return $insights->getArrayCopy();
    }

    /**
     * @param array<int, AdsInsights> $data
     * @return array<int, Ad>
     */
    protected function transform($data): array
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
