<?php


namespace Hyper\Functions;


trait Cast
{
    public static function toInstance(string $name, $entity): object
    {
        $d = $name;
        $obj = new $d();
        foreach ((array)$entity as $property => $value) {
            $obj->$property = $value;
        }
        return $obj;
    }
}