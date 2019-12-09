<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use function AdsWarehouse\yesterday;

/**
 * @template T
 */
abstract class ETL
{
    private Warehouse $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @return T
     */
    abstract protected function extract();

    /**
     * @param T $data
     * @return array<int, Ad>
     */
    abstract protected function transform($data): array;

    public function load(): void
    {
        $this->warehouse->drop($this->getSource(), yesterday());
        $this->warehouse->store($this->transform($this->extract()));
    }

    abstract protected function getSource(): string;
}
