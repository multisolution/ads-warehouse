<?php declare(strict_types=1);

namespace AdsWarehouse;

interface Resolver
{
    /**
     * @param mixed $root
     * @param array $args
     * @param Context $context
     * @return mixed
     */
    public function __invoke($root, array $args, Context $context);
}
