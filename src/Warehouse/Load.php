<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

/**
 * @template T
 */
interface Load
{
    /**
     * @param T[] $data
     * @return mixed
     */
    public function __invoke(array $data);
}
