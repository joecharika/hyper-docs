<?php


namespace Hyper\Database;


use Closure;
use Hyper\Functions\Debug;
use Hyper\Functions\Str;
use PDO;

trait ObjectQuery
{
    use Query, ForeignObject;

    /**
     * First object where condition is true
     * @param $column
     * @param $operator
     * @param $value
     * @return object|null
     */
    public function firstWhere($column, $operator, $value)
    {
        $stmt = $this->query(
            "SELECT * FROM `$this->tableName` WHERE $column$operator'$value' AND `deletedAt` is NULL",
            [],
            true
        );

        if ($stmt->setFetchMode(PDO::FETCH_ASSOC)) {
            $obj = $stmt->fetchObject($this->table);

            if ($obj !== false)
                return $this->attachForeignEntities($obj);
        }

        return null;
    }

    /**
     * Get the first element
     *
     * @param Closure|null $closure Function that takes an object and is supposed to return a bool.
     * @param array $with
     * @return object|null
     */
    public function first(Closure $closure = null, $with = [])
    {
        if (!isset($this->list)) $this->select();

        foreach ($this->list as $k => $v) {
            $entity = null;

            foreach ($with as $foreign) {
                $x = Str::pluralize($foreign);
                $v[$x] = (new DatabaseContext($foreign))->where($this->table . "Id", "=", $v['id'])->toList();
            }

            if (!isset($closure)) {
                $entity = $v;
            } elseif ($closure((object)$v)) $entity = $v;
            else return null;

            return $this->attachForeignEntities($entity);
        }

        return null;
    }

    /**
     * Get the first item matching a particular id
     *
     * @param $id
     * @param array $with
     * @return object|null
     */
    public function firstById($id, $with = [])
    {
        $stmt = $this->query("SELECT * FROM `$this->tableName` WHERE `id`=:id AND `deletedAt` is null", ["id" => $id],
            true);
        if ($stmt->setFetchMode(PDO::FETCH_ASSOC)) {
//            $obj = Arr::key($stmt->fetchAll(), 0, null);
            $obj = $stmt->fetchObject($this->table);

            if (isset($obj)) {
                foreach ($with as $foreign) {
                    $x = Str::pluralize($foreign);
                    $obj[$x] = (new DatabaseContext($foreign))->where($this->model . "Id", "=", $obj['id'])
                        ->toList();
                }

                return $this->attachForeignEntities($obj);
            }

        }
        return null;
    }
}