<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_GetReportsResponse;
use Google_Service_AnalyticsReporting_Metric as Metric;
use Google_Service_AnalyticsReporting_Report;
use Google_Service_AnalyticsReporting_ReportRequest;

class GoogleAnalytics extends ETL
{
    /** @var Google_Service_AnalyticsReporting */
    private $analytics;
    /** @var string */
    private $viewId;

    public function __construct(
        Warehouse $warehouse,
        Google_Service_AnalyticsReporting $analytics,
        string $viewId
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

        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->viewId);
        $request->setDateRanges($date_range);
        $request->setMetrics($this->getMetrics());

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests([$request]);

        return $this->analytics->reports->batchGet($body);
    }

    /**
     * @return Metric[]
     */
    private function getMetrics(): array
    {
        return array_map([$this, 'newMetricFromExpression'], $this->getExpressions());
    }

    /**
     * @return string[]
     */
    private function getExpressions(): array
    {
        return [
            'ga:users',
            'ga:newUsers',
            'ga:sessions',
        ];
    }

    /**
     * @param Google_Service_AnalyticsReporting_GetReportsResponse $data
     * @return Ad
     */
    protected function transform($data): Ad
    {
        /** @var Google_Service_AnalyticsReporting_Report $report */
        $report = $data->getReports()[0];

        $data = $report->getData();

        var_dump($data->getTotals());

        $ad = new Ad();
        return $ad;
    }

    private function newMetricFromExpression(string $expression): Metric
    {
        $metric = new Metric();
        $metric->setExpression($expression);

        return $metric;
    }
}
