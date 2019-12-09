<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Ad\Ad;
use DateTime;

interface Warehouse
{
    /**
     * @return array<int, Ad>
     */
    public function items(): array;

    /**
     * @param array<int, Ad> $ads
     */
    public function store(array $ads): void;

    public function drop(string $source, DateTime $date): void;
}
