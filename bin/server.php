<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\Http\Handler;
use Monolog\Handler\ErrorLogHandler;
use PDO;
use Siler\Monolog as Log;
use function Siler\Swoole\http;

$basedir = dirname(__DIR__, 1);
require_once "$basedir/vendor/autoload.php";

Log\handler(new ErrorLogHandler());

$pdo = new PDO(getenv('POSTGRES_DSN'));
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ERRMODE_EXCEPTION);

$context = new Context();
$context->schema = require_once "$basedir/src/schema.php";
$context->warehouse = new Warehouse\Pdo($pdo);
$context->debug = getenv('APP_DEBUG') === 'true';

http(new Handler($context), 8000)->start();
