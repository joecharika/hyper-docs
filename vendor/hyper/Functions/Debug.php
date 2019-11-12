<?php


namespace Hyper\Functions;


use Hyper\Application\HyperApp;

abstract class Debug
{
    /**
     * var_dump a variable and exit immediately in debug mode
     *
     * @param mixed $var
     */
    public static function dump($var)
    {
        if (HyperApp::$debug) {
            var_dump($var);
            exit(0);
        }
    }

    /**
     * var_dump a variable in debug mode
     *
     * @param mixed $var
     */
    public static function print($var)
    {
        if (HyperApp::$debug) {
            var_dump($var);
        }
    }

}