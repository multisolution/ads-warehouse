<?php declare(strict_types=1);

namespace AdsWarehouse\ETL;

use AdsWarehouse\Account\Account;
use AdsWarehouse\Ad\Ad;
use AdsWarehouse\Warehouse\Warehouse;
use function AdsWarehouse\yesterday;

/**
 * @template T
 */
abstract class ETL
{
    private Warehouse $warehouse;
    protected Account $account;

    public function __construct(Warehouse $warehouse, Account $account)
    {
        $this->warehouse = $warehouse;
        $this->account = $account;
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
