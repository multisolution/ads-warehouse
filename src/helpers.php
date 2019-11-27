<?php declare(strict_types=1);

namespace AdsWarehouse;

use DateTime;

function yesterday(): DateTime
{
    return (new DateTime())->modify('-1 day');
}
