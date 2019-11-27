<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\Resolver;

use AdsWarehouse\Context;
use AdsWarehouse\Resolver;

class Ads implements Resolver
{
    public function __invoke($root, array $args, Context $context)
    {
        return $context->warehouse->items();
    }
}

