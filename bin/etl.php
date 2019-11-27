<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\ETL\GoogleAnalytics;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Monolog\Handler\ErrorLogHandler;
use PDO;
use Siler\Monolog as Log;

$basedir = dirname(__DIR__, 1);
require_once "$basedir/vendor/autoload.php";

Log\handler(new ErrorLogHandler());

$pdo = new PDO(getenv('POSTGRES_DSN'));
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ERRMODE_EXCEPTION);

$context = new Context();
$context->warehouse = new Warehouse\Pdo($pdo);

$google_client = new Google_Client();
$google_client->setApplicationName('Ads Warehouse');
$google_client->setAuthConfig($basedir . DIRECTORY_SEPARATOR . getenv('GOOGLE_CREDENTIALS'));
$google_client->setScopes([Google_Service_Analytics::ANALYTICS_READONLY]);

$analytics = new Google_Service_AnalyticsReporting($google_client);
$analytics_etl = new GoogleAnalytics($context->warehouse, $analytics, getenv('GA_VIEW_ID'));

$analytics_etl->load();
