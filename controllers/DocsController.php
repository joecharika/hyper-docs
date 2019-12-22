<?php


namespace Controllers;


use DateTime;
use Helpers\ReflectionHelper;
use Hyper\Http\Request;
use Hyper\Controllers\Controller;
use Hyper\Exception\HyperError;
use Hyper\Exception\HyperException;
use Hyper\Exception\HyperHttpException;
use Hyper\Functions\Obj;
use ReflectionClass;
use ReflectionException;
use function file_exists;
use function scandir;
use function ucfirst;

/**
 * Class DocsController
 * @package Controllers
 * @author Hyper Team
 */
class DocsController extends Controller
{
    use HyperError;

    /**
     * Docs Index Action
     * @url [ /docs ]
     */
    public function index()
    {
        return $this->view('home.docs', null,null, [
            'lastUpdated' => (new DateTime('12/08/19'))->format('d l F Y'),
            'packages' => $this->getPackages()
        ]);
    }

    private function getPackages(): array
    {
        $packages = [];

        foreach (scandir('vendor/hyper') as $file) {
            if ($file != '.' && $file != '..' && is_dir('vendor/hyper/' . $file)) {
                $packages[] = $file;
            }
        }

        return $packages;
    }

    public function api()
    {

        $packages = $this->getPackages();
        $package = Request::params()->id ?? $packages[array_key_last($packages)];
        $package = Obj::property(Request::params(), 'param0') ? $package . '\\' . Request::params()->param0 : $package;
        $classes = $subPackages = [];

        if (!file_exists('vendor/hyper/' . $package)) (new HyperHttpException)->notFound();

        foreach (scandir('vendor/hyper/' . $package) as $file) {
            if ($file != '.' && $file != '..' && is_file('vendor/hyper/' . $package . '/' . $file)) {
                $class = str_replace('.php', '', $file);
                try {
                    $ref = new ReflectionClass('\\Hyper\\' . ucfirst($package) . '\\' . $class);
                    $classes[] = ReflectionHelper::getClass($ref);
                } catch (ReflectionException $e) {
                    self::error(new HyperException($e->getMessage()));
                }
            }
            if ($file != '.' && $file != '..' && is_dir('vendor/hyper/' . $package . '/' . $file))
                $subPackages[] = $file;
        }

        $model = (object)[
            'name' => ucfirst($package),
            'classes' => $classes,
            'packages' => $subPackages
        ];

        return $this->view('docs.package', $model,null, ['packages' => $packages]);
    }
}
