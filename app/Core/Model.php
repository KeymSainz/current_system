<?php
/**
 * Fix&Go — Base Model
 * All models extend this and get a shared DB connection.
 */
namespace App\Core;

abstract class Model
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
