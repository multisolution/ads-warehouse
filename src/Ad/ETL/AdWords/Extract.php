<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\ETL\AdWords;

use AdsWarehouse\Extract as ExtractInterface;
use Google_Service_AnalyticsReporting as AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Dimension as Dimension;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_GetReportsResponse as ReportsResponse;
use Google_Service_AnalyticsReporting_Metric as Metric;
use Google_Service_AnalyticsReporting_Report as Report;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_Resource_Reports as ReportsResource;

/**
 * @implements ExtractInterface<Report>
 */
class Extract implements ExtractInterface
{
    private AnalyticsReporting $analytics;
    private string $viewId;

    public function __construct(AnalyticsReporting $analytics, string $viewId)
    {
        $this->analytics = $analytics;
        $this->viewId = $viewId;
    }

    /**
     * @return Report[]
     */
    public function __invoke(): array
    {
        $date_range = new Google_Service_AnalyticsReporting_DateRange();
        $date_range->setStartDate('yesterday');
        $date_range->setEndDate('yesterday');

        $campaign_id = new Dimension();
        $campaign_id->setName('ga:campaign');

        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->viewId);
        $request->setDateRanges($date_range);
        $request->setMetrics($this->getMetrics());
        $request->setDimensions([$campaign_id]);

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests([$request]);

        /**
         * @psalm-suppress MissingPropertyType
         * @var ReportsResource $reports_resource
         */
        $reports_resource = $this->analytics->reports;

        /** @var ReportsResponse $response */
        $response = $reports_resource->batchGet($body);

        /** @var Report[] */
        return [$response->getReports()[0]]; // hacks!!!
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
            'ga:impressions',
            'ga:adClicks',
            'ga:adCost',
            'ga:CPM',
            'ga:CPC',
            'ga:CTR',
        ];
    }

    private function newMetricFromExpression(string $expression): Metric
    {
        $metric = new Metric();
        $metric->setExpression($expression);

        return $metric;
    }
}
