<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\ETL\AdWords;
use AdsWarehouse\ETL\ETL;
use AdsWarehouse\ETL\FacebookAds;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Monolog\Handler\ErrorLogHandler;
use PDO;
use Siler\Monolog as Log;

date_default_timezone_set(getenv('TZ'));

$basedir = dirname(__DIR__, 1);
require_once "$basedir/vendor/autoload.php";

Log\handler(new ErrorLogHandler());

$pdo = new PDO(getenv('POSTGRES_DSN'));
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ERRMODE_EXCEPTION);
$warehouse = new Warehouse\Pdo($pdo);

$google_client = new Google_Client();
$google_client->setApplicationName('Ads Warehouse');
$google_client->setAuthConfig($basedir . DIRECTORY_SEPARATOR . getenv('GOOGLE_CREDENTIALS'));
$google_client->setScopes([Google_Service_Analytics::ANALYTICS_READONLY]);

$analytics = new Google_Service_AnalyticsReporting($google_client);
$api = Api::init(getenv('FB_APP_ID'), getenv('FB_APP_SECRET'), getenv('FB_ACCESS_TOKEN'));

if (getenv('APP_DEBUG') === 'true') {
    $api->setLogger(new CurlLogger());
}

$elt = [
    new AdWords($warehouse, $analytics, getenv('GA_VIEW_ID')),
    new FacebookAds($warehouse, $api, getenv('FB_AD_ACCOUNT_ID')),
];

array_walk($elt, function (ETL $etl): void {
    $etl->load();
});
