<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\Http\Handler;
use function Siler\GraphQL\schema;
use function Siler\Swoole\http;

$basedir = dirname(__DIR__, 1);
require_once "$basedir/vendor/autoload.php";

$type_defs = graphql_files("$basedir/src/");
$resolvers = require_once "$basedir/src/resolvers.php";
$schema = schema($type_defs, $resolvers);

$context = new Context();
$context->schema = $schema;

http(new Handler($context), 8000)->start();