<?php

namespace Hyper\Application;

use Func\Twig\CompressExtension;
use Hyper\Database\DatabaseContext;
use Hyper\Exception\{HyperException, HyperHttpException, NullValueException};
use Hyper\Functions\Arr;
use Twig\{Environment,
    Error\LoaderError,
    Error\RuntimeError,
    Error\SyntaxError,
    Loader\FilesystemLoader,
    TwigFilter,
    TwigFunction};
use function array_merge;
use function class_exists;
use function explode;
use function is_null;
use function json_encode;
use function str_replace;

/**
 * Trait ControllerFunctions
 * @package hyper\Application
 */
trait ControllerFunctions
{
    /** @var DatabaseContext */
    public $db;

    /** @var string */
    public $model;

    /** @var string */
    public $layout;

    /** @var string */
    public $name;


    /**
     * ControllerFunctions constructor.
     */
    public function __construct()
    {
        $this->name = str_replace('Controller', '', str_replace('Controllers\\', '', static::class));
        $this->model = '\\Models\\' . $this->name;

        if (class_exists($this->model))
            $this->db = new DatabaseContext($this->name);

        if (is_null($this->layout)) $this->layout = 'layout';
    }

    /**
     * Convert $data to json and print it out
     * @param mixed $data
     */
    public function json($data)
    {
        if (is_null($data)) (new NullValueException)->throw();
        print(json_encode($data));
    }


    /**
     * @param string $view
     * @param null $model
     * @param array $vars
     */
    public function view(string $view, $model = null, $vars = [])
    {
        $array = explode('.', $view);
        $folder = Arr::key($array, 0, '');
        $view = Arr::key($array, 1, '');

        $loader = new FilesystemLoader('views/');
        $twig = new Environment($loader);

        HyperApp::emitEvent(HyperEventHook::renderingStarting, $twig);
        $twig->addExtension(new CompressExtension());

        $this->addTwigFilters($twig);
        $this->addTwigFunctions($twig);

        try {
            echo $twig->render($folder . '/' . $view . '.html.twig',
                array_merge(
                    [
                        'model' => $model,
                        'user' => HyperApp::$user,
                        'request' => (object)array_merge([
                            'url' => Request::url(),
                            'protocol' => Request::protocol(),
                            'path' => Request::path(),
                            'previousUrl' => Request::previousUrl(),
                            'query' => Request::query(),
                        ], Request::notification()),
                        'appName' => HyperApp::$name,
                        'route' => Request::$route ?? HyperApp::$route,
                    ],
                    $vars
                )
            );
            HyperApp::emitEvent(HyperEventHook::renderingCompleted, $twig);
        } catch (LoaderError $e) {
            (new HyperHttpException)->notFound($e->getMessage());
        } catch (RuntimeError $e) {
            (new HyperException)->throw($e->getMessage() . ' on line: ' . $e->getLine() . ' in ' . $e->getFile());
        } catch (SyntaxError $e) {
            (new HyperException)->throw($e->getMessage() . ' on line: ' . $e->getLine() . ' in ' . $e->getFile());
        }

    }

    #region Extending Twig

    /**
     * @param Environment $twig
     */
    private function addTwigFilters(Environment &$twig)
    {
        #Cast object to array
        $twig->addFilter(new TwigFilter('toArray', function ($object) {
            return (array)$object;
        }));

        #Cast array to object
        $twig->addFilter(new TwigFilter('toObject', function ($array) {
            return (object)$array;
        }));

        #Cast array to object
        $twig->addFilter(new TwigFilter('isArray', function ($array) {
            return is_array($array);
        }));

        $twig->addFilter(new TwigFilter('toPascal', 'Str::toPascal'));
        $twig->addFilter(new TwigFilter('toCamel', 'Str::toCamel'));
    }

    /**
     * @param Environment $twig
     */
    private function addTwigFunctions(Environment &$twig)
    {
        $twig->addFunction(new TwigFunction('img', function ($image) {
            return Request::protocol() . '://' . Request::server() . '/assets/img/' . $image;
        }));
        $twig->addFunction(new TwigFunction('css', function ($stylesheet) {
            return Request::protocol() . '://' . Request::server() . '/assets/css/' . $stylesheet;
        }));
        $twig->addFunction(new TwigFunction('js', function ($script) {
            return Request::protocol() . '://' . Request::server() . '/assets/js/' . $script;
        }));
        $twig->addFunction(new TwigFunction('asset', function ($asset) {
            return Request::protocol() . '://' . Request::server() . '/assets/' . $asset;
        }));
    }

    #endregion

}
