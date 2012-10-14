<?php

/**
 * Basic application of LMVC
 */
class LVC {

    /**
     * @var LVC singleton object
     */
    private static $object=null;

    /**
     * @var array application setting from config.json
     */
    private static $config=array();

    /**
     * @var string name of the currently used controller
     */
    private $controller;

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
    private $request=array();

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
    private $view=null;

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
    private function __construct() {
        $this->protocol = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
        $this->host = $_SERVER['HTTP_HOST'];
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $this->uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $slug = explode('/', $_GET['app-slug']);
        $slug = $this->setController($slug);
        $this->params = $this->setAction($slug);
        $this->request = array_slice($_GET,1);
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
    public static function get() {
        if (is_null(self::$object)) {
            self::$object = new LVC();
        }
        return self::$object;
    }

    /**
     * @static
     * @param string $configFile file name of the json based cofing file
     * @return void
     */
    public static function initialize($configFile=null) {
        if (!is_null($configFile) && file_exists($configFile)) {
            LVC::configure(json_decode(file_get_contents($configFile)));
        } else {
            LVC::configure((object)array(
                'appPath'  => './',
                'appModulePath' => './modules/',
                'frameworkPath' => '../lmvc/framework/',
                'modulePath' => '../lmvc/modules/',
                'paths' => array('controllers', 'framework')
            ));
        }
    }

    /**
     * configures the application and activates auto loader
     *
     * @static
     * @param array $config assotiative array of all configurations
     * @return void
     */
    private static function configure($config) {
        self::$config = $config;
        self::setAutoload();
    }

    /**
     * activates auto loader from configuration
     *
     * @static
     * @return void
     */
    private static function setAutoload() {
        $path  = PATH_SEPARATOR . self::$config->frameworkPath . PATH_SEPARATOR . self::$config->frameworkPath .
            implode(PATH_SEPARATOR . self::$config->frameworkPath, self::$config->paths);
        $path .= PATH_SEPARATOR . self::$config->appPath .
            implode(PATH_SEPARATOR . self::$config->appPath, self::$config->paths);
        if (!empty(self::$config->appModulePath)) {
            $path .= self::getModulePaths(self::$config->appModulePath);
        }
        if (!empty(self::$config->modulePath)) {
            $path .= self::getModulePaths(self::$config->modulePath);
        }
        set_include_path(get_include_path() . $path);
        spl_autoload_register(function ($classname) {
            @include($classname.'.php');
        });
    }

    /**
     * scans the module path and adds all modules to a path string
     *
     * @static
     * @param string $modulePath the path that will be scanned
     * @return string include path
     */
    private static function getModulePaths($modulePath) {
        $result = '';
        $modules = array_filter(scandir($modulePath), function($dirFile) {
            return (!is_file($dirFile) && $dirFile != '.' && $dirFile != '..');
        });
        foreach ($modules as $module) {
            $result .= PATH_SEPARATOR . $modulePath . $module . DIRECTORY_SEPARATOR .
                implode(PATH_SEPARATOR . $modulePath . $module . '/' , self::$config->paths);
        }
        return $result;
    }

    /**
     * Dispatcher called by index.php in application root
     *
     * @static
     * @return void
     */
    public static function dispatch() {
        self::get()->run();
    }

    /**
     * sets the controller from slug
     *
     * @param array $slug the URL divided in pieces
     * @return array the reduced slug
     */
    private function setController($slug) {
        $this->controller = ucfirst(LVC::camelCaseFrom($slug[0]));
        if (!class_exists($this->controller)) {
            $this->controller = 'Application';
        } else {
            $slug = array_slice($slug,1);
        }
        return $slug;
    }

    /**
     * sets the action from slug
     *
     * @param $slug the URL divided in pieces
     * @return array the reduced slug
     */
    private function setAction($slug) {
        $this->action = self::camelCaseFrom($slug[0]);
        $this->actionName = $this->action;
        if (is_callable($this->controller . '::' . strtolower($this->requestMethod) . ucfirst($this->action))) {
            $this->action = strtolower($this->requestMethod) . ucfirst($this->action);
            $slug = array_slice($slug,1);
        } elseif (is_callable($this->controller . '::' . $this->action)) {
            $slug = array_slice($slug,1);
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
    public function __get($name) {
        if (in_array($name, array('controller', 'action', 'actionName', 'requestMethod', 'host', 'uri', 'params', 'view', 'protocol', 'referer'))) {
            $result = $this->$name;
        } elseif (in_array($name, array('request'))) {
            $result = (object)$this->$name;
        } elseif (in_array($name, array('config'))) {
            $result = (object)self::$config;
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
    public function __set($name, $value) {
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
    public function url($method, $params=null) {
        if ($params && !is_array($params)) {
            $params = array($params);
        }
        $method = explode('::', $method);
        $controller = ($method[0] == 'Application') ? '/' : '/' . self::camelCaseTo($method[0]);
        $action = ($method[1] == 'index') ? '/' : '/' . self::camelCaseTo($method[1]);
        return $this->protocol . '://' .
            $this->host .
            $this->uri .
            (($controller == '/' && $action != '/') ? '' : $controller) .
            (($action == '/') ? '' : $action) .
            (($params) ?  '/' . implode('/', $params) : '');
    }

    /**
     * runs the http request
     *
     * @return void
     */
    public function run() {
        call_user_func_array($this->controller . '::preProcess', $this->params);
        call_user_func_array($this->controller . '::' . $this->action, $this->params);
        call_user_func_array($this->controller . '::postProcess', $this->params);
    }

    /**
     * Helper for string manipulation
     *
     * @static
     * @param string $camelCasedString any camelCasedString
     * @param string $delimiter optional default is '-'
     * @return string a lower cased string with a delimiter before each hump
     */
    public static function camelCaseTo($camelCasedString, $delimiter='-') {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/',$delimiter . "$1", $camelCasedString));
    }

    /**
     * Helper for string manipulation
     *
     * @static
     * @param string $otherString any string like 'test-string'
     * @param string $delimiter optional default is '-'
     * @return string a camelCasedString with humps for each found delimiter
     */
    public static function camelCaseFrom($otherString, $delimiter='-') {
        return lcfirst(
            implode('',
                array_map(function($data) { return ucfirst($data); }, explode($delimiter, $otherString))
            )
        );
    }

}