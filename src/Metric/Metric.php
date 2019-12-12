<?php declare(strict_types=1);

namespace AdsWarehouse\Metric;

use AdsWarehouse\Account\Account;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Metric
{
    /** @var UuidInterface|string */
    public $id;
    public Account $account;
    public string $key;
    public int $value;

    public function __construct()
    {
        if (isset($this->id) && is_string($this->id)) {
            $this->id = Uuid::fromString($this->id);
        }
    }
}
