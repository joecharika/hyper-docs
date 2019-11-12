<?php


namespace Hyper\Application;


class Event
{
    public $name, $data;

    public function __construct($name, $data)
    {
        $this->name = $name;
        $this->data = $data;
    }
}