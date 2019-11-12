<?php


namespace Hyper\SQL;


/**
 * Class Query
 * @package Hyper\SQL
 */
class Query extends QueryBuilder
{
    /**
     * @return string
     */
    public function toSql()
    {
        return $this->getQuery();
    }
}