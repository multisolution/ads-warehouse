<?php declare(strict_types=1);

namespace AdsWarehouse\Account;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Account
{
    /** @var UuidInterface|string */
    public $id;
    public string $name;
    public ?string $gaViewId;
    public ?string $fbAdAccountId;

    public function __construct()
    {
        if (isset($this->id) && is_string($this->id)) {
            $this->id = Uuid::fromString($this->id);
        }

        if (isset($this->ga_view_id)) {
            $this->gaViewId = strval($this->ga_view_id);
        }

        if (isset($this->fb_ad_account_id)) {
            $this->fbAdAccountId = strval($this->fb_ad_account_id);
        }
    }
}
