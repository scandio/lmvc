<?php

class App {
	
	private static $object=null;
    private static $config=array();

	private $controller;
	private $action;
	private $params;
	private $request=array();
	private $renderArgs=array();
	private $requestMethod;
	private $host;
	private $uri;
    private $pdo=null;

    private function __construct() {
        $this->host = $_SERVER['HTTP_HOST'];
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

	public static function get() {
		if (is_null(self::$object)) {
			self::$object = new App();
		}
		return self::$object;
	}

    public static function initialize($configFile=null) {
        if (!is_null($configFile)) {
            App::configure(json_decode(file_get_contents($configFile)));
        } else {
            App::configure((object)array('db' => 'sqlite:db.sq3', 'appPath'  => './', 'frameworkPath' => './framework/', 'modulePath' => './modules/', 'paths' => array('controllers', 'models', 'framework')));
        }
    }

    private static function configure($config) {
        self::$config = $config;
        self::setAutoload();
    }

    private static function setAutoload() {
        $modules = array_filter(scandir(self::$config->modulePath), function($dirFile) {
            return (!is_file($dirFile) && $dirFile != '.' && $dirFile != '..');
        });
        set_include_path(get_include_path() . self::getPath($modules));
        spl_autoload_register(function ($classname){
            spl_autoload($classname);
        });
    }

    private static function getPath($modules=array()) {
        $result  = PATH_SEPARATOR . self::$config->frameworkPath . implode(PATH_SEPARATOR . self::$config->frameworkPath, self::$config->paths);
        $result .= PATH_SEPARATOR . self::$config->appPath . implode(PATH_SEPARATOR . self::$config->appPath, self::$config->paths);
        foreach ($modules as $module) {
            $result .= PATH_SEPARATOR . self::$config->modulePath . $module . '/' . implode(PATH_SEPARATOR . self::$config->modulePath . $module . '/' , self::$config->paths);
        }
        return $result;
    }

    public static function dispatch() {
        self::get()->run();
    }

    public function db() {
        if(is_null($this->pdo)) {
            $this->pdo = new PDO(self::$config->db);
        }
        return $this->pdo;
    }

	private function setController($slug) {
		$this->controller = ucfirst(App::camelCaseFrom($slug[0]));
		if (!class_exists($this->controller)) {
			$this->controller = 'Application';
		} else {
			$slug = array_slice($slug,1);
		}
		return $slug;
	}
	
	private function setAction($slug) {
		$this->action = self::camelCaseFrom($slug[0]);
		if (is_callable($this->controller . '::' . strtolower($this->requestMethod) . ucfirst($this->action))) {
			$this->action = strtolower($this->requestMethod) . ucfirst($this->action);
			$slug = array_slice($slug,1);
		} elseif (is_callable($this->controller . '::' . $this->action)) {
			$slug = array_slice($slug,1);
		} else {
			$this->action = 'index';
		}
		return $slug;
	}

	public function __get($name) {
        if (in_array($name, array('controller', 'action', 'requestMethod', 'host', 'uri', 'renderArgs'))) {
            $result = $this->$name;
        } elseif (in_array($name, array('request'))) {
            $result = (object)$this->$name;
        } elseif (in_array($name, array('config'))) {
            $result = (object)self::$config;
        }
        return $result;
	}
	
    public function __set($name, $value) {
		if ($name == 'renderArgs' && is_array($value)) {
			$this->renderArgs = $value;
		}
	}
	
	public function setRenderArg($name, $value) {
		$this->renderArgs[$name] = $value;
	}

	public function run() {
        call_user_func_array($this->controller . '::preProcess', $this->params);
        call_user_func_array($this->controller . '::' . $this->action, $this->params);
        call_user_func_array($this->controller . '::postProcess', $this->params);
	}

    public static function camelCaseTo($camelCasedString, $delimiter='-') {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/',$delimiter . "$1", $camelCasedString));
    }

    public static function camelCaseFrom($otherString, $delimiter='-') {
        return lcfirst(implode('', array_map(function($data) { return ucfirst($data); }, explode($delimiter, $otherString))));
    }

}