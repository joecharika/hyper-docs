<?php


namespace Hyper\Database;


use Closure;
use Hyper\Exception\HyperException;
use Hyper\Exception\NullValueException;
use PDO;
use PDOException;

trait FilterQuery
{
    use Query, ListQuery, ForeignObject;

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return FilterQuery
     */
    public function where($column, $operator, $value): FilterQuery
    {
        $stmt = $this->query("SELECT * FROM `$this->tableName` WHERE $column$operator'$value' AND `deletedAt` is NULL",
            [], true);

        if ($stmt->setFetchMode(PDO::FETCH_ASSOC)) {
            $arr = [];

            foreach ($stmt->fetchAll() as $entity) {
                array_push($arr, $this->attachForeignEntities($entity));
            }

            $this->list = $arr;
        }

        return $this;
    }

    /**
     * @param $search
     * @return DatabaseContext
     */
    public function search($search): DatabaseContext
    {
        if (empty($search)) {
            $this->select();
            return $this;
        }

        $searchArray = [];

        foreach (get_class_vars($this->table) as $classVar => $value) {
            array_push($searchArray, "`$this->tableName`.`$classVar` LIKE :search");
        }

        $searchString = implode(" OR ", $searchArray);

        $stmt = $this->query("SELECT * FROM `$this->tableName` WHERE  `deletedAt` is NULL AND ($searchString)",
            [":search" => "%$search%"], true);

        $arr = [];

        if ($stmt->setFetchMode(PDO::FETCH_ASSOC)) {
            foreach ($stmt->fetchAll() as $entity) {
                array_push($arr, $this->attachForeignEntities($entity));
            }
        }

        $this->list = $arr;

        return $this;
    }

    /**
     * @param Closure $condition
     * @return DatabaseContext|FilterQuery
     */
    public function whereClosure(Closure $condition)
    {
        if (!isset($condition)) (new NullValueException)->throw("Closure cannot be null.");

        try {
            $this->select();
            $arr = [];
            foreach ($this->list as $k => $v) {
                if ($condition((object)$v)) array_push($arr, (object)$v);
            }
            $this->list = $arr;
        } catch (PDOException $e) {
            (new HyperException)->throw($e->getMessage());
        }
        return $this;
    }

    /**
     * @param $column
     * @param null $limit
     * @param null $offset
     * @return float
     */
    public function sum($column, $limit = null, $offset = null): float
    {
        $isLimited = !isset($limit) ? '' : "LIMIT $limit";
        $isOffset = !isset($offset) ? '' : "OFFSET $offset";

        $stmt = $this->query(
            "SELECT SUM(`$this->tableName`.`$column`) FROM `$this->tableName` WHERE `deletedAt` is NULL $isLimited $isOffset",
            [],
            true
        );

        if ($stmt->setFetchMode(PDO::FETCH_ASSOC))
            return (float)$stmt->fetch();

        return 0;
    }
}