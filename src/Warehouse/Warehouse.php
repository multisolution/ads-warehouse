<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Ad\Ad;

interface Warehouse
{
    /**
     * @psalm-return list<Ad>
     * @return Ad[]
     */
    public function items(): array;
}
