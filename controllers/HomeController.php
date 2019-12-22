<?php


namespace Controllers;


use Hyper\Controllers\Controller;

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
        return $this->view('home.index');
    }

    /**
     * Home Docs Action
     * @url [ /home/docs, /docs ]
     */
    public function docs()
    {
        return (new DocsController)->index();
    }
}