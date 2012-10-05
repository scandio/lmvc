<?php

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
abstract class Controller {

    /**
     * @var array assotitive array of values for rendering
     */
    private static $renderArgs=array();

    /**
     * set a single render argument which is used
     * in render methods.
     *
     * @static
     * @param string $name name will be converted in $<name> in a view
     * @param mixed $value any kind of value.
     * @return void
     */
    public static function setRenderArg($name, $value) {
        self::$renderArgs[$name] = $value;
    }

    /**
     * Replaces the renderArg Array completely or merges it if
     * $add is set to true
     *
     * @static
     * @param array $renderArgs an assotiative array
     * @param bool $add optional set to true if you want to merge existing data with $renderArgs
     * @return void
     */
    public static function setRenderArgs($renderArgs, $add=false) {
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
    public static function request() {
        return LVC::get()->request;
    }

    /**
     * renders the $renderArgs Array to a valid JSON output
     * if there are complex objects in $renderArgs you need
     * to develop a own ArrayBuilder class for conversion
     *
     * @static
     * @param array $renderArgs optional an assotiative array of values
     * @param ArrayBuilder $arrayBuilder optional your converter class based on ArrayBuilder interface
     * @return void
     */
    public static function renderJson($renderArgs=null, ArrayBuilder $arrayBuilder=null) {
        if (is_object($renderArgs) && $arrayBuilder instanceof ArrayBuilder) {
            $renderArgs = $arrayBuilder::build($renderArgs);
        }
        self::setRenderArgs($renderArgs, true);
        foreach (self::$renderArgs as $key => $renderArg) {
            if (is_object($renderArg) && $arrayBuilder instanceof ArrayBuilder) {
                $result[$key] = $arrayBuilder::build($renderArg);
            } else {
                $result[$key] = $renderArg;
            }
        }
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1964 07:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($result);
    }

    /**
     * Renders a HTML view. It loads the corresponding view automatically
     * The naming convention for a view is Application::index() opens views/application/index.html
     * and views/main.html have to exists as master template. You can overwrite
     * template and master template with optional parameters. The method extracts all elements
     * of $renderArgs to local variables which may be used in the template
     *
     * @static
     * @param array $renderArgs optional an assotiative array of values
     * @param string $template optional a file name like 'views/test/test.html' which overwrites the default
     * @param string $masterTemplate optional a file name like 'views/test/test.html' which overwrites the default master
     * @return void
     */
    public static function render($renderArgs=array(), $template=null, $masterTemplate=null) {
        self::setRenderArgs($renderArgs, true);
        extract(self::$renderArgs);
        $app = LVC::get();
        if($template) {
            $app->view = $app->config->appPath . $template;
        } else {
            $app->view = $app->config->appPath . 'views/' .
                LVC::camelCaseTo($app->controller) . '/' .
                LVC::camelCaseTo($app->actionName) . '.html';
            if (!file_exists($app->view)) {
                $reflection = new ReflectionClass(get_called_class());
                $classFileName = $reflection->getFileName();
                $reducer = 'controllers/' . $app->controller . '.php';
                $module = end(explode('/', substr($classFileName, 0,  strpos($classFileName, $reducer)-1)));
                $app->view = $app->config->modulePath . $module .
                    '/views/' . strtolower($app->controller) .
                    '/' . strtolower($app->actionName) . '.html';
            }
        }
        if (!is_null($masterTemplate)) {
            include($masterTemplate);
        } else {
            include($app->config->appPath . 'views/main.html');
        }
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
     * @return void
     */
    public static function redirect($method, $params=null) {
        header('Location: ' . LVC::get()->url($method, $params));
    }

    /**
     * override it if your controller need a pre processor method
     *
     * @static
     * @return void
     */
    public static function preProcess() {

    }

    /**
     * override it if your controller needs a post processor method
     *
     * @static
     * @return void
     */
    public static function postProcess() {

    }

}