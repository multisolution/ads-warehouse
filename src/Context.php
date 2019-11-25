<?php declare(strict_types=1);

namespace AdsWarehouse;

use GraphQL\Type\Schema;

class Context
{
    /** @var Schema */
    public $schema;
    /** @var mixed */
    public $rootValue;
}