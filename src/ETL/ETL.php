<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Warehouse\Warehouse;

abstract class ETL
{
    /** @var Warehouse */
    private $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    abstract protected function getSource(): string;
}
