<?php


namespace Controllers;


use Helpers\ReflectionHelper;
use Hyper\Application\Controller;
use Hyper\Application\Request;
use Hyper\Exception\HyperException;
use Hyper\Exception\HyperHttpException;

/**
 * Class DocsController
 * @package Controllers
 * @author Hyper Team
 */
class DocsController extends Controller
{
    /**
     * Docs Index Action
     * @url [ /docs ]
     */
    public function index()
    {
        self::view('home.docs', null, [
            'lastUpdated' => (new \DateTime('11/08/19'))->format('d l F Y'),
            'packages' => $this->getPackages()
        ]);
    }

    public function api()
    {
        $packages = $this->getPackages();
        $package = Request::params()->id ?? $packages[array_key_last($packages)];
        $classes = [];

        if(!\file_exists('vendor/hyper/' . $package)) (new HyperHttpException)->notFound();

        foreach (\scandir('vendor/hyper/' . $package) as $file) {
            if ($file != '.' && $file != '..') {
                $class = str_replace('.php', '', $file);
                try {
                    $ref = new \ReflectionClass('\\Hyper\\' . ucfirst($package) . '\\' . $class);
                    $classes[] = ReflectionHelper::getClass($ref);
                } catch (\ReflectionException $e) {
                    (new HyperException)->throw($e->getMessage());
                }
            }
        }

        $model = (object)[
            'name' => \ucfirst($package),
            'classes' => $classes
        ];

        self::view('docs.package', $model, ['packages' => $packages]);
    }

    private function getPackages(): array
    {
        $packages = [];

        foreach (\scandir('vendor/hyper') as $file) {
            if ($file != '.' && $file != '..' && is_dir('vendor/hyper/' . $file)) {
                $packages[] = $file;
            }
        }

        return $packages;
    }

}
