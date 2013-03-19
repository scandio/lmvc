<?php

namespace Scandio\lmvc\framework;

/**
 * Basic application of LMVC
 */
class LVC
{

    /**
     * @var array default configuration to use if a property was not provided
     */
    private static $defaultConfig = array(
        'appPath' => './',
        'controllers' => array('\\controllers'),
        'viewPath' => array('./views/'),
        'modules' => array()
    );

    /**
     * @var LVC singleton object
     */
    private static $object = null;

    /**
     * @var array application setting from config.json
     */
    private static $config = array();

    /**
     * @var string name of the currently used controller
     */
    private $controller;

    /**
     * @var string fully qualified namespace of the currently used controller
     */
    private $controllerNamespace;

    /**
     * @var string name of the currently used action method
     */
    private $action;

    /**
     * @var string real name of the current action without post, get, put
     */
    private $actionName;

    /**
     * @var array list of parameter values from the URL
     */
    private $params;

    /**
     * @var array all GET, POST, PUT and DELETE request variables
     */
    private $request = array();

    /**
     * @var string requestMethod like GET, POST, PUT and DELETE
     */
    private $requestMethod;

    /**
     * @var string hostname of webserver
     */
    private $host;

    /**
     * @var string server relative uri of the application
     */
    private $uri;

    /**
     * @var string filename of the current view
     */
    private $view = null;

    /**
     * @var string http | https
     */
    private $protocol;

    /**
     * @var string referer
     */
    private $referer;

    /**
     * Private constructor which creates the singleton object
     *
     * @return void
     */
    private function __construct()
    {
        $this->protocol = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
        $this->host = $_SERVER['HTTP_HOST'];
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $this->uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $slug = explode('/', $_GET['app-slug']);
        $slug = $this->setController($slug);
        $this->params = $this->setAction($slug);
        $this->request = array_slice($_GET, 1);
        $this->request = array_merge($this->request, $_POST);
        if ($this->requestMethod == 'PUT' || $this->requestMethod == 'DELETE') {
            parse_str(file_get_contents('php://input'), $params);
            $this->request = array_merge($params);
        }
    }

    /**
     * returns the instance of the single ton object
     * use it like LVC::get() from outside
     *
     * @static
     * @return LVC
     */
    public static function get()
    {
        if (is_null(self::$object)) {
            self::$object = new LVC();
        }
        return self::$object;
    }

    /**
     * @static
     * @param string $configFile file name of the json based config file
     * @return void
     */
    public static function initialize($configFile = null)
    {
        if (!is_null($configFile) && file_exists($configFile)) {
            LVC::configure(json_decode(file_get_contents($configFile)), self::$defaultConfig);
        } else {
            LVC::configure((object)self::$defaultConfig);
        }
    }

    /**
     * configures the application
     *
     * @static
     * @param object $config all configurations
     * @param array $default associative array with default configurations
     * @return void
     */
    private static function configure($config, $default=null)
    {
        if (!is_null($default)) {
            foreach (array_keys($default) as $entry) {
                if (!isset($config->$entry)) {
                    $config->$entry = $default[$entry];
                }
            }
        }
        self::$config = $config;

        foreach (self::getModulePaths($config->modules) as $modulePath) {
            self::registerBootstrapNamespace($modulePath);
        }

        if (isset($config->appNamespace)) {
            self::registerBootstrapNamespace($config->appNamespace);
        }
    }

    /**
     * splits up the namespace specification(s) and determines their namespaces
     *
     * @static
     * @param object|array|string $module namespace specification(s) for modules to load
     * @return array determined namespaces
     */
    private static function getModulePaths($module)
    {
        $modulePaths = array();

        if (is_object($module)) {
            foreach (get_object_vars($module) as $package => $submodules) {
                foreach (self::getModulePaths($submodules) as $submodule) {
                    $modulePaths[] = $package . '\\' . $submodule;
                }
            }
        } elseif (is_array($module)) {
            foreach ($module as $entry) {
                foreach (self::getModulePaths($entry) as $modulePath) {
                    $modulePaths[] = $modulePath;
                }
            }
        } elseif (is_string($module)) {
            $modulePaths[] = $module;
        } else {
            echo PHP_EOL . "<!-- Couldn't register ModuleNamespace:" . PHP_EOL;
            var_dump($module);
            echo "-->" . PHP_EOL;
        }

        return $modulePaths;
    }

    /**
     * Used to initialize modules and potentially the app itself
     *
     * @static
     * @param string $module namespace specification as a string that contains a class 'Bootstrap'
     * @return void
     */
    private static function registerBootstrapNamespace($module)
    {
        $bootstrap = $module . '\\Bootstrap';
        if (class_exists($bootstrap)) {
            $moduleLoader = new $bootstrap;
            if (is_subclass_of($moduleLoader, '\\Scandio\\lmvc\\framework\\Bootstrap')) {
                $moduleLoader->initialize();
            }
        }
    }

    /**
     * registers a new controller namespace to search for the controllers
     *
     * @static
     * @param object|array|string $controller namespace specification (a controller instance or specified as strings)
     * @return void
     */
    public static function registerControllerNamespace($controller)
    {
        if (is_object($controller)) {
            $namespace = implode('\\', array_slice(explode('\\', get_class($controller)), 0, -1));
        } elseif (is_array($controller)) {
            $namespace = implode('\\', $controller);
        } elseif (is_string($controller)) {
            $namespace = $controller;
        } else {
            echo PHP_EOL . "<!-- Couldn't register ControllerNamespace:" . PHP_EOL;
            var_dump($controller);
            echo "-->" . PHP_EOL;
            return;
        }
        array_unshift(self::$config->controllers, $namespace);
    }

    /**
     * registers a new view directory to search for the views
     *
     * @static
     * @param array|string $path specifies the directory to register
     * @return void
     */
    public static function registerViewDirectory($path)
    {
        if (is_array($path)) {
            $viewPath = implode(DIRECTORY_SEPARATOR, $path);
        } elseif (is_string($path)) {
            $viewPath = $path;
        } else {
            echo PHP_EOL . "<!-- Couldn't register ViewDirectory:" . PHP_EOL;
            var_dump($path);
            echo "-->" . PHP_EOL;
            return;
        }
        array_unshift(self::$config->viewPath, $viewPath);
    }

    /**
     * Dispatcher called by index.php in application root
     *
     * @static
     * @return void
     */
    public static function dispatch()
    {
        self::get()->run();
    }

    /**
     * sets the controller from slug
     *
     * @param array $slug the URL divided in pieces
     * @return array the reduced slug
     */
    private function setController($slug)
    {
        $this->controller = ucfirst(LVC::camelCaseFrom($slug[0]));

        if (!self::searchController()) {
            $this->controller = 'Application';
            if (!self::searchController()) {
                echo PHP_EOL . "<!-- Couldn't find either the Controller '" . ucfirst(LVC::camelCaseFrom($slug[0])) .
                    "' or '" . $this->controller . "' in the following namespaces:" . PHP_EOL . PHP_EOL;
                var_dump(self::$config->controllers);
                echo "-->" . PHP_EOL;
                exit;
            }
        } else {
            $slug = array_slice($slug, 1);
        }
        return $slug;
    }

    /**
     * searches for the controller in the registered namespaces
     *
     * @return bool whether the controller's full qualified class name could be determined
     */
    private function searchController()
    {
        $controllerFound = false;

        foreach (self::$config->controllers as $path) {
            if (class_exists($path . '\\' . $this->controller)) {
                $this->controllerNamespace = $path;
                $controllerFound = true;
                break;
            }
        }

        return $controllerFound;
    }

    /**
     * sets the action from slug
     *
     * @param $slug the URL divided in pieces
     * @return array the reduced slug
     */
    private function setAction($slug)
    {
        $this->action = LVC::camelCaseFrom($slug[0]);
        $this->actionName = $this->action;
        if (is_callable($this->controllerFQCN . '::' . strtolower($this->requestMethod) . ucfirst($this->action))) {
            $this->action = strtolower($this->requestMethod) . ucfirst($this->action);
            $slug = array_slice($slug, 1);
        } elseif (is_callable($this->controllerFQCN . '::' . $this->action)) {
            $slug = array_slice($slug, 1);
        } else {
            $this->action = 'index';
            $this->actionName = $this->action;
        }
        return $slug;
    }

    /**
     * Some getters needed in other contexts
     *
     * @param string $name the name of the variable
     * @return mixed the requested value
     */
    public function __get($name)
    {
        if (in_array($name, array(
            'action',
            'actionName',
            'controller',
            'controllerNamespace',
            'host',
            'params',
            'protocol',
            'referer',
            'requestMethod',
            'uri',
            'view'
        ))
        ) {
            $result = $this->$name;
        } elseif (in_array($name, array('request'))) {
            $result = (object)$this->$name;
        } elseif (in_array($name, array('config'))) {
            $result = (object)self::$$name;
        } elseif (in_array($name, array('controllerFQCN'))) { // Fully Qualified Class Name
            $result = $this->controllerNamespace . '\\' . $this->controller;
        }
        return $result;
    }

    /**
     * setters needed
     *
     * @param string $name the name of the variable
     * @param mixed $value the value to set
     * @return void
     */
    public function __set($name, $value)
    {
        if ($name == 'view') {
            $this->view = $value;
        }
    }

    /**
     * generates an URL from a static class method with parameters
     * from url('Application::index', $var) you get the URL
     * /app/path/controller/action/{$var}
     *
     * @param string $method method name in static syntax like 'Application::index'
     * @param string|array $params single value or array of parameters
     * @return string the URL
     */
    public function url($method, $params = null)
    {
        if ($params && !is_array($params)) {
            $params = array($params);
        }
        $method = explode('::', $method);
        $controller = ($method[0] == 'Application') ? '/' : '/' . LVC::camelCaseTo($method[0]);
        $action = ($method[1] == 'index') ? '/' : '/' . LVC::camelCaseTo($method[1]);
        return $this->protocol . '://' .
            $this->host .
            $this->uri .
            (($controller == '/' && $action != '/') ? '' : $controller) .
            (($action == '/') ? '' : $action) .
            (($params) ? (($controller == '/' && $action == '/') ? '' : '/') . implode('/', $params) : '');
    }

    /**
     * runs the http request
     *
     * @return void
     */
    public function run()
    {
        call_user_func_array($this->controllerFQCN . '::preProcess', $this->params);
        call_user_func_array($this->controllerFQCN . '::' . $this->action, $this->params);
        call_user_func_array($this->controllerFQCN . '::postProcess', $this->params);
    }

    /**
     * Helper for string manipulation
     *
     * @static
     * @param string $camelCasedString any camelCasedString
     * @param string $delimiter optional default is '-'
     * @return string a lower cased string with a delimiter before each hump
     */
    public static function camelCaseTo($camelCasedString, $delimiter = '-')
    {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', $delimiter . "$1", $camelCasedString));
    }

    /**
     * Helper for string manipulation
     *
     * @static
     * @param string $otherString any string like 'test-string'
     * @param string $delimiter optional default is '-'
     * @return string a camelCasedString with humps for each found delimiter
     */
    public static function camelCaseFrom($otherString, $delimiter = '-')
    {
        return lcfirst(
            implode('',
                array_map(function ($data) {
                    return ucfirst($data);
                }, explode($delimiter, $otherString))
            )
        );
    }

}