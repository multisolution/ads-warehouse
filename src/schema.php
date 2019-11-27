<?php declare(strict_types=1);

namespace AdsWarehouse;

use function Siler\File\concat_files;
use function Siler\File\recur_iter_dir;
use function Siler\GraphQL\schema;

$basedir = dirname(__DIR__, 1);
$type_defs = concat_files(recur_iter_dir("$basedir/src", '/\.graphql$/'));
$resolvers = require_once "$basedir/src/resolvers.php";

return schema($type_defs, $resolvers);
