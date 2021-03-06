<?php

namespace Hyper\Functions;


use function is_array;
use function is_object;

/**
 * Trait Cast
 * @package Hyper\Functions
 */
trait Cast
{
    /**
     * @param string $name
     * @param $entity
     * @return object
     */
    public static function toInstance(string $name, $entity): object
    {
        $obj = new $name();
        foreach ((array)$entity as $property => $value) {
            if (!is_object($value) && !is_array($value))
                $obj->$property = $value;
        }
        return $obj;
    }
}