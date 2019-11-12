<?php


namespace Hyper\SQL;


/**
 * Class SQLComparisonOperator
 * @package Hyper\SQL
 */
abstract class SQLComparisonOperator
{
    /**
     * Checks if the values of two operands are equal or not, if yes then condition becomes true.
     */
    const equal = '=';

    /**
     * Checks if the values of two operands are equal or not, if values are not equal then condition becomes true.
     */
    const notEqual = '!=';

    /**
     * Checks if the values of two operands are equal or not, if values are not equal then condition becomes true.
     */
    const notEqualFancy = '<>';

    /**
     * Checks if the value of left operand is less than the value of right operand, if yes then condition becomes true.
     */
    const lessThan = '<';

    /**
     * Checks if the value of left operand is not less than the value of right operand, if yes then condition becomes true.
     */
    const notLessThan = '!<';

    /**
     * Checks if the value of left operand is greater than the value of right operand, if yes then condition becomes true.
     */
    const greaterThan = '>';

    /**
     * Checks if the value of left operand is not greater than the value of right operand, if yes then condition becomes true.
     */
    const notGreaterThan = '!>';

    /**
     * Checks if the value of left operand is less than or equal to the value of right operand, if yes then condition becomes true.
     */
    const lessThanOrEqual = '<=';

    /**
     * Checks if the value of left operand is greater than or equal to the value of right operand, if yes then condition becomes true.
     */
    const greaterThanOrEqual = '>=';
}