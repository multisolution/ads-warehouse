<?php declare(strict_types=1);

namespace AdsWarehouse\Ad\Resolver;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Context;
use AdsWarehouse\Resolver;

class Ads implements Resolver
{
    /**
     * @psalm-param mixed $root
     * @psalm-return list<Ad>
     * @return Ad[]
     */
    public function __invoke($root, array $args, Context $context): array
    {
        return $context->warehouse->items();
    }
}
