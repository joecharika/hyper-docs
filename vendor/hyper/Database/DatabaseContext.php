<?php
/**
 * hyper v1.0.0-beta.2 (https://hyper.com/php)
 * Copyright (c) 2019. J.Charika
 * Licensed under MIT (https://github.com/joecharika/hyper/master/LICENSE)
 */

namespace Hyper\Database;

use Hyper\Application\{HyperApp};
use Hyper\Exception\{HyperException, NullValueException};
use Hyper\Functions\{Arr, Str};
use Hyper\Models\Pagination;
use Hyper\Reflection\Annotation;
use PDO;
use PDOException;
use function class_exists;
use function get_class_vars;
use function strpos;

/**
 * Class DatabaseContext
 * @package hyper\Database
 */
class DatabaseContext
{
    use Database, Query, CRUDQuery, ForeignObject, FilterQuery, ListQuery, ObjectQuery;

    #region Properties

    /** @var Pagination */
    public $pagination;

    #endregion


    /**
     * DatabaseContext constructor.
     * @param string $model
     * @param DatabaseConfig $config
     */
    public function __construct(string $model, DatabaseConfig $config = null)
    {
        $this->model = $model;
        $this->table = '\\Models\\' . ucfirst($model);
        $this->tableName = Str::pluralize("$model");
        $this->config = $config ?? HyperApp::$dbConfig;

        if (isset($this->config))
            $this->failSafe();
        else (new NullValueException)->throw('Database configuration has to be set.');
    }

    #region Database

    /**
     * Failsafe call that will create the database and table if they do not exist
     */
    private function failSafe()
    {
        try {
            $this->connect();
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos("$msg", "Unknown database") > 0) $this->createDatabase();
            else (new HyperException)->throw($msg);
        }

        $this->createTable();
    }

    /**
     * Initialise database connection for the rest of the execution
     *
     * @throws PDOException
     */
    private function connect()
    {
        $db = HyperApp::config()->db;
        $servername = $db->host;
        $username = $db->username;
        $database = $db->database;
        $password = $db->password;
        try {
            $this->db = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (!isset($this->db)) (new HyperException)->throw("Failed to connect");
        } catch (PDOException $e) {
            throw $e;
        }
    }

    #endregion

    #region Query

    /**
     * Create database from config: web.hyper.json
     * @return void
     */
    private function createDatabase()
    {
        $db = HyperApp::$dbConfig;

        try {
            $conn = new PDO("mysql:host=$db->host", $db->username, $db->password);

            if (!isset($conn)) (new HyperException())->throw('Failed to connect to server');
            if (!$conn->query("CREATE DATABASE `$db->database`")) (new HyperException())->throw("Failed to create database.");

        } catch (PDOException $exception) {
            (new HyperException())->throw($exception->getMessage());
        }
    }

    /**
     * Create if not exists table
     *
     * @return DatabaseContext
     */
    private function createTable(): DatabaseContext
    {
        $d = $this->query('show tables', [], true);

        if (array_search($this->tableName, Arr::key($d->fetchAll(PDO::FETCH_NUM), 0, [])) === false) {
            try {
                $this->connect();
                $properties = "";

                if (class_exists($this->table)) {
                    $classVars = get_class_vars($this->table);
                    foreach ($classVars as $classVar => $value) {
                        $type = $this->getDataType($value, $classVar);
                        $sqlAttrs = Annotation::getPropertyAnnotation($this->table, $classVar, "SQLAttributes") ?? "";
                        $hasDefault = !isset($value) ? "" : (empty($value) ? "" : "DEFAULT $value");
                        $properties .= "`$classVar` $type $sqlAttrs $hasDefault,";
                    }
                    $properties = $this->addTimeStamps($properties);
                    if ($this->query("CREATE TABLE IF NOT EXISTS `$this->tableName`($properties)") === false)
                        (new HyperException)->throw("Failed to create table.");
                } else {
                    (new HyperException)->throw("Model($this->table) does not exist");
                }
            } catch (PDOException $e) {
                (new HyperException)->throw($e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Get the sql data type
     * @param $value
     * @param $name
     * @return string
     */
    private function getDataType($value, $name)
    {
        if (is_bool($value)) return "BIT";
        elseif (is_int($value)) return "INT";
        elseif (is_string($value)) return "TEXT";
        else return Annotation::getPropertyAnnotation($this->table, $name, "SQLType") ?? "TEXT";
    }

    /**
     * @param string $properties
     * @return string
     */
    private function addTimeStamps(string $properties)
    {
        return $properties . "createdAt TIMESTAMP null, updatedAt TIMESTAMP null, deletedAt TIMESTAMP null";
    }

    #endregion

}