<?php


namespace Hyper\Database;


use Hyper\Exception\HyperException;
use Hyper\Exception\NullValueException;
use Hyper\Functions\Debug;
use Hyper\Functions\Obj;
use Hyper\Functions\Str;

trait ForeignObject
{
    use Database, ObjectQuery;

    /**
     * @param $entity
     * @return object
     */
    private function attachForeignEntities($entity)
    {
        if (isset($entity)) {
            if (!is_array($entity)) $entity = (array)$entity;
            foreach ($this->foreignKeys() as $foreign) {
                $entity[$foreign] = (new DatabaseContext($foreign))->firstById($entity[$foreign . 'Id']);
            }
            return Obj::toInstance($this->table, $entity);
        }
        return null;
    }

    /**
     * @return array
     */
    public function foreignKeys(): array
    {
        $foreignKeys = [];
        if (class_exists($this->table)) {
            $classVars = get_class_vars($this->table);
            foreach ($classVars as $classVar => $value) {
                $classVar = strtolower($classVar);
                if (strpos($classVar, "id") && $classVar !== "id")
                    array_push($foreignKeys, strtr($classVar, ['id' => '']));
            }
        } else (new HyperException)->throw("Model($this->table) does not exist");

        return $foreignKeys;
    }

    /**
     * Add compound lists of specified items that rely on this model
     * @param array $foreignList
     * @return DatabaseContext|ForeignObject
     */
    public function with(array $foreignList)
    {
        if (!isset($foreignList)) (new NullValueException)->throw("models to attach cannot be null.");

        if (!isset($this->list)) $this->select();

        $arr = [];
        foreach ($foreignList as $foreign) {
            foreach ($this->list as $k => $v) {
                $d = (object)$v;
                $x = Str::pluralize($foreign);
                $d->$x = (new DatabaseContext($foreign))->where($this->model . 'Id', '=', $d->id)->toList();
                array_push($arr, $this->attachForeignEntities($v));
            }
        }
        $this->list = $arr;

        return $this;
    }
}