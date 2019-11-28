<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use function AdsWarehouse\yesterday;

abstract class ETL
{
    /** @var Warehouse */
    private $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @return mixed
     */
    abstract protected function extract();

    /**
     * @param mixed $data
     * @return Ad[]
     * @psalm-return list<Ad>
     */
    abstract protected function transform($data): array;

    public function load(): void
    {
        $this->warehouse->drop($this->getSource(), yesterday());
        $this->warehouse->store($this->transform($this->extract()));
    }

    abstract protected function getSource(): string;
}
