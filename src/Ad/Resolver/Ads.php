<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\Resolver;

use AdsWarehouse\Context;
use AdsWarehouse\Resolver;

class Ads implements Resolver
{
    /** @psalm-return array<int, Ad> */
    public function __invoke($root, array $args, Context $context): array
    {
        return $context->warehouse->items();
    }
}
