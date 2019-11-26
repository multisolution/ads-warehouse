<?php declare(strict_types=1);

namespace AdsWarehouse;

interface Resolver
{
    public function __invoke($root, array $args, Context $context);
}
