<?php


namespace Hyper\SQL;


class SQLType
{
    public static function type($type, $size = null): QueryBuilder
    {
        if (isset($size)) $type = "$type($size)";
        return new QueryBuilder($type);
    }

    public static function int($size = 10): QueryBuilder
    {
        return self::type('int', $size);
    }

    public static function decimal(): QueryBuilder
    {
    }

    public static function newDecimal(): QueryBuilder
    {
    }

    public static function float(): QueryBuilder
    {
    }

    public static function double(): QueryBuilder
    {
    }

    public static function bit(): QueryBuilder
    {
    }

    public static function tiny(): QueryBuilder
    {
    }

    public static function short(): QueryBuilder
    {
    }

    public static function long(): QueryBuilder
    {
    }

    public static function longLong(): QueryBuilder
    {
    }

    public static function int24(): QueryBuilder
    {
    }

    public static function enum(): QueryBuilder
    {
    }

    public static function timestamp(): QueryBuilder
    {
    }

    public static function date(): QueryBuilder
    {
    }

    public static function time(): QueryBuilder
    {
    }

    public static function datetime(): QueryBuilder
    {
    }

    public static function newDate(): QueryBuilder
    {
    }

    public static function interval(): QueryBuilder
    {
    }

    public static function set(): QueryBuilder
    {
    }

    public static function varString(): QueryBuilder
    {
    }

    public static function string(): QueryBuilder
    {
    }

    public static function char(): QueryBuilder
    {
    }

    public static function geometry(): QueryBuilder
    {
    }

    public static function blob(): QueryBuilder
    {
    }

    public static function tinyBlob(): QueryBuilder
    {
    }

    public static function mediumBlob(): QueryBuilder
    {
    }

    public static function longBlob(): QueryBuilder
    {
    }

    public static function text(): QueryBuilder
    {
    }

}