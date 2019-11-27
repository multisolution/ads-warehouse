<?php declare(strict_types=1);

namespace AdsWarehouse\Ad;

use DateTime;

class Ad
{
    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var float */
    public $cost;
    /** @var int */
    public $impressions;
    /** @var int */
    public $clicks;
    /** @var float */
    public $cpm;
    /** @var float */
    public $cpc;
    /** @var float */
    public $ctr;
    /** @var string */
    public $source;
    /** @var DateTime */
    public $date;

    public function __construct()
    {
        if (isset($this->date) && is_string($this->date)) {
            $this->date = DateTime::createFromFormat('Y-m-d', $this->date);
        }
    }
}
