<?php declare(strict_types=1);

namespace AdsWarehouse;

use AdsWarehouse\Warehouse\Warehouse;
use GraphQL\Type\Schema;

class Context
{
    /** @var Schema */
    public $schema;
    /** @var mixed */
    public $rootValue;
    /** @var Warehouse */
    public $warehouse;

    /** @var bool */
    public $debug;
}
