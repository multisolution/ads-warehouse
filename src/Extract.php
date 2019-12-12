<?php declare(strict_types=1);

namespace AdsWarehouse;

/**
 * @template T
 */
interface Extract
{
    /**
     * @return T[]
     */
    public function __invoke(): array;
}
