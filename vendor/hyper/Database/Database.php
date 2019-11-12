<?php


namespace Hyper\Database;


use PDO;

trait Database
{
    /** @var PDO */
    private $db;

    /** @var DatabaseConfig */
    private $config;

    /** @var string */
    private $table;

    /** @var string */
    private $tableName;

    /** @var string */
    private $model;
}