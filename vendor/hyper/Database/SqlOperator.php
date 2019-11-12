<?php


namespace Hyper\Database;


/**
 * Class SqlOperator
 * @package Hyper\Database
 *
 * All of the available clause operators
 */
abstract class SqlOperator
{


    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'ilike',
        '&', '|', '<<', '>>',
    ];

}