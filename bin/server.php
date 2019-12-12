<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\Http\Handler;
use GraphQL\Type\Schema;
use Monolog\Handler\ErrorLogHandler;
use PDO;
use Siler\Monolog as Log;
use function Siler\Env\env_bool;
use function Siler\Env\env_var;
use function Siler\Swoole\http;

require_once __DIR__ . '/../vendor/autoload.php';

Log\handler(new ErrorLogHandler());

$pdo = new PDO(env_var('POSTGRES_DSN'));
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::ERRMODE_EXCEPTION);

$context = new Context();
/** @var Schema schema */
$context->schema = require_once __DIR__ . '/../src/schema.php';
$context->warehouse = new Warehouse\Pdo($pdo);
$context->debug = env_bool('APP_DEBUG');

http(new Handler($context), 8000)->start();
