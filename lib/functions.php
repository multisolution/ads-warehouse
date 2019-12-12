<?php declare(strict_types=1);

function iterator_map(Iterator $iterator, callable $callback): array
{
    $aggregation = [];

    foreach ($iterator as $key => $val) {
        $aggregation[$key] = $val;
    }

    return $aggregation;
}
