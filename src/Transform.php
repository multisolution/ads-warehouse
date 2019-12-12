<?php declare(strict_types=1);

namespace AdsWarehouse;

/**
 * @template T0
 * @template T1
 */
interface Transform
{
    /**
     * @param T0[] $data
     * @return T1[]
     */
    public function __invoke(array $data): array;
}
