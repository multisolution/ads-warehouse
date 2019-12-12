<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Account\Account;

interface Accounts
{
    /**
     * @return Account[]
     */
    public function __invoke(): array;
}
