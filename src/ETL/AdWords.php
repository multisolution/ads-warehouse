<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use DateTime;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_DateRangeValues;
use Google_Service_AnalyticsReporting_Dimension as Dimension;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_GetReportsResponse;
use Google_Service_AnalyticsReporting_Metric as Metric;
use Google_Service_AnalyticsReporting_Report;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_ReportRow;
use Google_Service_AnalyticsReporting_Resource_Reports;
use Ramsey\Uuid\Uuid;

/**
 * @template-extends ETL<Google_Service_AnalyticsReporting_GetReportsResponse>
 */
class AdWords extends ETL
{
    private const SOURCE = 'AdWords';

    private Google_Service_AnalyticsReporting $analytics;
    private int $viewId;

    public function __construct(
        Warehouse $warehouse,
        Google_Service_AnalyticsReporting $analytics,
        int $viewId
    )
    {
        parent::__construct($warehouse);
        $this->analytics = $analytics;
        $this->viewId = $viewId;
    }

    protected function extract(): Google_Service_AnalyticsReporting_GetReportsResponse
    {
        $date_range = new Google_Service_AnalyticsReporting_DateRange();
        $date_range->setStartDate('yesterday');
        $date_range->setEndDate('yesterday');

        $campaignId = new Dimension();
        $campaignId->setName('ga:campaign');

        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->viewId);
        $request->setDateRanges($date_range);
        $request->setMetrics($this->getMetrics());
        $request->setDimensions([$campaignId]);

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests([$request]);

        /**
         * @psalm-suppress MissingPropertyType
         * @var Google_Service_AnalyticsReporting_Resource_Reports $reports
         */
        $reports = $this->analytics->reports;

        return $reports->batchGet($body);
    }

    /**
     * @param Google_Service_AnalyticsReporting_GetReportsResponse $data
     * @return array<int, Ad>
     */
    protected function transform($data): array
    {
        /** @var Google_Service_AnalyticsReporting_Report $report */
        $report = $data->getReports()[0];

        $data = $report->getData();
        $ads = [];

        /** @var Google_Service_AnalyticsReporting_ReportRow $row */
        foreach ($data->getRows() as $row) {
            /** @var string[] $dimensions */
            $dimensions = $row->getDimensions();
            /** @var string $campaign */
            $campaign = $dimensions[0];
            /** @var Google_Service_AnalyticsReporting_DateRangeValues $metrics */
            $metrics = $row->getMetrics()[0];
            /** @var array $values */
            $values = $metrics->getValues();

            $ad = new Ad();
            $ad->id = Uuid::uuid4();
            $ad->name = $campaign;
            $ad->impressions = intval($values[3]);
            $ad->clicks = intval($values[4]);
            $ad->cost = floatval($values[5]);
            $ad->cpm = floatval($values[6]);
            $ad->cpc = floatval($values[7]);
            $ad->ctr = floatval($values[8]);
            $ad->source = $this->getSource();
            $ad->date = (new DateTime())->modify('-1 day');

            $ads[] = $ad;
        }

        return $ads;
    }

    /** @return array<int, string> */
    private function getExpressions(): array
    {
        return [
            'ga:users',
            'ga:newUsers',
            'ga:sessions',
            'ga:impressions',
            'ga:adClicks',
            'ga:adCost',
            'ga:CPM',
            'ga:CPC',
            'ga:CTR',
        ];
    }

    /** @return array<int, Metric> */
    private function getMetrics(): array
    {
        return array_map([$this, 'newMetricFromExpression'], $this->getExpressions());
    }

    private function newMetricFromExpression(string $expression): Metric
    {
        $metric = new Metric();
        $metric->setExpression($expression);

        return $metric;
    }

    protected function getSource(): string
    {
        return self::SOURCE;
    }
}
