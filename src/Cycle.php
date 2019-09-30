<?php


namespace Laracycle;


use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseManager;

class Cycle
{
    /**
     * @var ORM
     */
    private $orm;
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    public function __construct(ORM $orm, DatabaseManager $databaseManager)
    {
        $this->orm = $orm;
        $this->databaseManager = $databaseManager;
    }

    public function orm(): ORM
    {
        return $this->orm;
    }

    public function transaction(): Transaction
    {
        return new Transaction($this->orm);
    }

    public function database(string $db = null): DatabaseInterface
    {
        return $this->databaseManager->database($db);
    }
}
