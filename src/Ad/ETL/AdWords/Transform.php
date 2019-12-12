<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\ETL\AdWords;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Transform as TransformInterface;
use DateTime;
use Google_Service_AnalyticsReporting_DateRangeValues as DateRangeValues;
use Google_Service_AnalyticsReporting_Report as Report;
use Google_Service_AnalyticsReporting_ReportRow as ReportRow;
use Ramsey\Uuid\Uuid;

/**
 * @implements TransformInterface<Report, Ad>
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
     * @param Report[] $data
     * @return Ad[]
     */
    public function __invoke(array $data): array
    {
        $values = $data[0]->getData();
        /** @var ReportRow[] $rows */
        $rows = $values->getRows();

        return array_map(function (ReportRow $row): Ad {
            /** @var string[] $dimensions */
            $dimensions = $row->getDimensions();
            /** @var string $campaign */
            $campaign = $dimensions[0];
            /** @var DateRangeValues $metrics */
            $metrics = $row->getMetrics()[0];
            /** @var array $values */
            $values = $metrics->getValues();

            $ad = new Ad();
            $ad->id = Uuid::uuid4();
            $ad->account = $this->account;
            $ad->name = $campaign;
            $ad->impressions = intval($values[3]);
            $ad->clicks = intval($values[4]);
            $ad->cost = floatval($values[5]);
            $ad->cpm = floatval($values[6]);
            $ad->cpc = floatval($values[7]);
            $ad->ctr = floatval($values[8]);
            $ad->source = $this->source;
            $ad->date = (new DateTime())->modify('-1 day');

            return $ad;
        }, $rows);
    }
}
