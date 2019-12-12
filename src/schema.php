<?php declare(strict_types=1);

namespace AdsWarehouse;

use function Siler\File\{concat_files, recur_iter_dir};
use function Siler\GraphQL\schema;

$type_defs = concat_files(recur_iter_dir(__DIR__ . '/../src', '/\.graphql$/'));

/** @var array<string, mixed> $resolvers */
$resolvers = require_once __DIR__ . '/../src/resolvers.php';

return schema($type_defs, $resolvers);
