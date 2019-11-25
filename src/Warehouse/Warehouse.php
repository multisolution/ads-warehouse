<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Ad\Ad;

interface Warehouse
{
    public function store(Ad $ad): bool;
}