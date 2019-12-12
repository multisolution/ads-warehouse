<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Ad\ETL\{AdWords, FacebookAds};
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Monolog\Handler\ErrorLogHandler;
use PDO;
use Siler\Monolog as Log;
use function Siler\Env\{env_bool, env_var};
use function Siler\Functional\compose;

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set(env_var('TZ'));

Log\handler(new ErrorLogHandler());

$pdo = new PDO(env_var('POSTGRES_DSN'));
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ERRMODE_EXCEPTION);

$google_client = new Google_Client();
$google_client->setApplicationName('Ads Warehouse');
$google_client->setAuthConfig(__DIR__ . '/../' . env_var('GOOGLE_CREDENTIALS'));
$google_client->setScopes([Google_Service_Analytics::ANALYTICS_READONLY]);

$analytics = new Google_Service_AnalyticsReporting($google_client);
$api = Api::init(env_var('FB_APP_ID'), env_var('FB_APP_SECRET'), env_var('FB_ACCESS_TOKEN'));

if (env_bool('APP_DEBUG')) {
    $api->setLogger(new CurlLogger());
}

$accounts = new Warehouse\PDO\Accounts($pdo);

/** @var array<int, callable> $pipelines */
$pipelines = array_map(function (Account $account) use ($analytics, $pdo, $api) {
    $pipeline = [];

    if ($account->gaViewId !== null) {
        $pipeline[] = compose([
            new Warehouse\PDO\AdsLoad($pdo),
            new AdWords\Transform($account, 'AdWords'),
            new AdWords\Extract($analytics, $account->gaViewId),
        ]);
    }

    if ($account->fbAdAccountId !== null) {
        $pipeline[] = compose([
            new Warehouse\PDO\AdsLoad($pdo),
            new FacebookAds\Transform($account, 'Facebook'),
            new FacebookAds\Extract($api, $account->fbAdAccountId),
        ]);
    }

    return $pipeline;
}, $accounts());

array_walk_recursive(
    $pipelines,
    /**
     * @param callable(mixed): mixed $pipeline
     * @return mixed
     */
    fn(callable $pipeline) => $pipeline(null)
);
