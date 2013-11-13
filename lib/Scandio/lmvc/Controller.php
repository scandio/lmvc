<?php

namespace Scandio\lmvc;

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
     * Renders a HTML view. It loads the corresponding view automatically
     * The naming convention for a view is Application::index() opens views/application/index.html
     * and views/main.html have to exists as master template. You can overwrite
     * template and master template with optional parameters. The method extracts all elements
     * of $renderArgs to local variables which may be used in the template
     *
     * @static
     * @param array $renderArgs optional an associative array of values
     * @param string $template optional a file name like 'views/test/test.html' which overwrites the default
     * @param int $httpCode
     * @param string $masterTemplate optional a file name like 'views/test/test.html' which overwrites the default master
     * @return bool
     */
    public static function render($renderArgs = array(), $template = null, $httpCode = 200, $masterTemplate = null)
    {
        if (isset(Config::get()->rendererClass) && class_exists(Config::get()->rendererClass)) {
            $rendererClass = Config::get()->rendererClass;
        } else {
            $rendererClass = '\Scandio\lmvc\PHPRenderer';
        }
        return $rendererClass::render($renderArgs, $template, $httpCode, $masterTemplate);
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