<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use DateTime;

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
        $this->warehouse->drop($this->getSource(), (new DateTime())->modify('-1 day'));
        $this->warehouse->store($this->transform($this->extract()));
    }

    /**
     * @param $data
     * @return Ad[]
     */
    abstract protected function transform($data): array;

    abstract protected function extract();

    abstract protected function getSource(): string;
}
