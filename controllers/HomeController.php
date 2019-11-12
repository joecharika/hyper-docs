<?php


namespace Controllers;


use Hyper\Application\Controller;
use Hyper\Exception\HyperException;

/**
 * Class HomeController
 * @package Controllers
 * @author Hyper Team
 */
class HomeController extends Controller
{
    /**
     * Home Index Action
     * @url [ /, /home ]
     */
    public function index()
    {
        self::view('home.index');
    }

    /**
     * Home Docs Action
     * @url [ /home/docs, /docs ]
     */
    public function docs()
    {
        (new DocsController)->index();
    }
}