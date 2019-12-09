<?php declare(strict_types=1);

namespace AdsWarehouse\Ad;

use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Ad
{
    /** @var UuidInterface|string */
    public $id;
    public string $name;
    public float $cost;
    public int $impressions;
    public int $clicks;
    public float $cpm;
    public float $cpc;
    public float $ctr;
    public string $source;
    /** @var DateTime|string */
    public $date;

    public function __construct()
    {
        if (is_string($this->id)) {
            $this->id = Uuid::fromString($this->id);
        }

        if (is_string($this->date)) {
            $this->date = DateTime::createFromFormat('Y-m-d', $this->date);
        }
    }
}
