<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;

abstract class ETL
{
    /** @var Warehouse */
    private $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    public function load()
    {
        $this->warehouse->store($this->transform($this->extract()));
    }

    abstract protected function transform($data): Ad;

    abstract protected function extract();
}
