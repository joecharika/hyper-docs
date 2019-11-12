<?php


namespace Hyper\SQL;


/**
 * Class SQLLogicalOperator
 * @package Hyper\SQL
 */
abstract class SQLLogicalOperator
{
    /**
     * ALL The ALL operator is used to compare a value to all values in another value set.
     */
    const all = 'all';
    /**
     *  AND The AND operator allows the existence of multiple conditions in an SQL statement's WHERE clause.
     */
    const and = 'and';
    /**
     *  ANY The ANY operator is used to compare a value to any applicable value in the list according to the condition.
     */
    const any = 'any';
    /**
     *  BETWEEN The BETWEEN operator is used to search for values that are within a set of values, given the minimum value and the maximum value.
     */
    const between = 'between';
    /**
     *  EXISTS The EXISTS operator is used to search for the presence of a row in a specified table that meets certain criteria.
     */
    const exists = 'exists';
    /**
     *  IN The IN operator is used to compare a value to a list of literal values that have been specified.
     */
    const in = 'in';
    /**
     *  NOT The NOT operator reverses the meaning of the logical operator with which it is used. Eg: NOT EXISTS, NOT BETWEEN, NOT IN, etc. This is a negate operator.
     */
    const not = 'not';
    /**
     *  OR The OR operator is used to combine multiple conditions in an SQL statement's WHERE clause.
     */
    const or = 'or';
    /**
     *  IS NULL The NULL operator is used to compare a value with a NULL value.
     */
    const is = 'is';
    /**
     *  UNIQUE The UNIQUE operator searches every row of a specified table for uniqueness (no duplicates).
     */
    const unique = 'unique';
}