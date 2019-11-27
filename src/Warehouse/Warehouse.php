<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Ad\Ad;
use DateTime;

interface Warehouse
{
    /**
     * @param Ad[] $ads
     */
    public function store(array $ads): void;

    /**
     * @return Ad[]
     */
    public function items(): array;

    public function drop(string $source, DateTime $date): void;
}
