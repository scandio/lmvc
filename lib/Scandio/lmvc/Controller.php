<?php

namespace Scandio\lmvc;

use \Scandio\lmvc\LVC;

/**
 * Static class for each action controller
 *
 * Controller is able to render HTML and
 * creates an JSON Output. Empty pre processor
 * and post processor methods for further usage
 * are implemented.
 *
 * @author ckoch
 * @abstract
 */
abstract class Controller
{
    /**
     * @var array associative array of values for rendering
     */
    private static $renderArgs = array();

    /**
     * set a single render argument which is used
     * in render methods.
     *
     * @static
     * @param string $name name will be converted in $<name> in a view
     * @param mixed $value any kind of value.
     * @return void
     */
    public static function setRenderArg($name, $value)
    {
        self::$renderArgs[$name] = $value;
    }

    /**
     * Replaces the renderArg Array completely or merges it if
     * $add is set to true
     *
     * @static
     * @param array $renderArgs an associative array
     * @param bool $add optional set to true if you want to merge existing data with $renderArgs
     * @return void
     */
    public static function setRenderArgs($renderArgs, $add = false)
    {
        if (!is_null($renderArgs)) {
            self::$renderArgs = ($add) ? array_merge(self::$renderArgs, $renderArgs) : $renderArgs;
        }
    }

    /**
     * Shorthand to the request data of the http request
     * self::request()->myVar == $_REQUEST['myVar']
     *
     * @static
     * @return object simple PHP object with all request data
     */
    public static function request()
    {
        return LVC::get()->request;
    }

    /**
     * renders the $renderArgs Array to a valid JSON output
     * if there are complex objects in $renderArgs you need
     * to develop a own ArrayBuilder class for conversion
     *
     * if it's set a callback method name in the GET parameter
     * a java script method will be submitted
     *
     * @static
     * @param null|array|object $renderArgs optional an associative array of values
     * @param int $httpCode optional a valid http status code like 200, 403, 404 or 500 defaults to 200
     * @param ArrayBuilderInterface $arrayBuilder optional your converter class based on ArrayBuilder interface
     * @return bool
     */
    public static function renderJson($renderArgs = null, $httpCode = 200, ArrayBuilderInterface $arrayBuilder = null)
    {
        http_response_code($httpCode);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1964 07:00:00 GMT');
        $json = static::buildJson($renderArgs, $arrayBuilder);
        if (isset($_GET['callback']) && !empty($_GET['callback'])) {
            $callback = $_GET['callback'];
            $json = $callback . '( return ' . $json . ');';
            header('Content-type: application/javascript');
        } else {
            header('Content-type: application/json');
        }
        echo $json;
        return true;
    }

    /**
     * @param null|array $renderArgs
     * @param ArrayBuilderInterface $arrayBuilder
     * @return string
     */
    protected static function buildJson($renderArgs = null, ArrayBuilderInterface $arrayBuilder = null) {
        $result = [];
        if (is_object($renderArgs) && $arrayBuilder instanceof ArrayBuilderInterface) {
            $renderArgs = $arrayBuilder::build($renderArgs);
        }
        self::setRenderArgs($renderArgs, true);
        foreach (self::$renderArgs as $key => $renderArg) {
            if (is_object($renderArg) && $arrayBuilder instanceof ArrayBuilderInterface) {
                $result[$key] = $arrayBuilder::build($renderArg);
            } else {
                $result[$key] = $renderArg;
            }
        }
        return json_encode($result);
    }

    /**
     * Renders a HTML view. It loads the corresponding view automatically
     * The naming convention for a view is Application::index() opens views/application/index.html
     * and views/main.html have to exists as master template. You can overwrite
     * template and master template with optional parameters. The method extracts all elements
     * of $renderArgs to local variables which may be used in the template
     *
     * @static
     * @param array $renderArgs optional an associative array of values
     * @param string $template optional a file name like 'views/test/test.html' which overwrites the default
     * @param string $masterTemplate optional a file name like 'views/test/test.html' which overwrites the default master
     * @return bool
     */
    public static function render($renderArgs = array(), $template = null, $httpCode = 200, $masterTemplate = null)
    {
        http_response_code($httpCode);
        self::setRenderArgs($renderArgs, true);
        extract(self::$renderArgs);
        $app = LVC::get();
        if ($template) {
            $app->view = $app->config->appPath . $template;
        } else {
            $app->view = self::searchView(LVC::camelCaseTo($app->controller) . '/' . LVC::camelCaseTo($app->actionName) . '.html');
        }
        if (!is_null($masterTemplate)) {
            $masterTemplate = $app->config->appPath . $masterTemplate;
        } else {
            $masterTemplate = self::searchView('main.html');
        }
        include($masterTemplate);
        return true;
    }

    /**
     * searches for the view in the registered directories
     *
     * @static
     * @param $view
     * @return string|bool either the view's full path or false
     */
    private static function searchView($view)
    {
        $config = LVC::get()->config;
        foreach ($config->viewPath as $path) {
            $viewPath = ((substr($path, 0, 1) == '/') ? '' : $config->appPath) . $path . '/' . $view;
            if (file_exists($viewPath)) {
                return $viewPath;
            }
        }
        return false;
    }

    /**
     * redirect from a controller action to another URL. It's possible to
     * use variables as dynamic URL parts. call it like
     * self::redirect('Application::index', $var); which redirects to
     * /app/path/controller/action/{$var}
     *
     * @static
     * @param string $method name of class and action in static syntax like Application::index without brackets
     * @param string|array $params optional one value or an array of values to enhance the generated URL
     * @return bool
     */
    public static function redirect($method, $params = null)
    {
        header('Location: ' . LVC::get()->url($method, $params));
        return true;
    }

    /**
     * override it if your controller need a pre processor method
     *
     * @static
     * @return bool
     */
    public static function preProcess()
    {
        return true;
    }

    /**
     * override it if your controller needs a post processor method
     *
     * @static
     * @return bool
     */
    public static function postProcess()
    {
        return true;
    }
}
