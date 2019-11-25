<?php declare(strict_types=1);

namespace AdsWarehouse\Warehouse;

use AdsWarehouse\Ad\Ad;

class Pdo implements Warehouse
{
    /** @var \PDO */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(Ad $ad): bool
    {
        $stmt = $this->pdo->prepare('insert into ad (id, name) values (:id, :name)');
        $stmt->bindValue('id', $ad->id);
        $stmt->bindValue('name', $ad->name);

        return $stmt->execute();
    }
}