<?php

class App {
	
	private static $object=null;
	private $controller;
	private $action;
	private $params;
	private $request=array();
	private $renderArgs=array();
	private $requestMethod;
	private $host;
	private $uri;
	private $config=array();
	
	static function dispatch($configFile='config.json') {
		self::get()->run($configFile);
	}

	static function get() {
		if (is_null(self::$object)) {
			self::$object = new App();
		}
		return self::$object;
	}
	
	private function setController($slug) {
		$this->controller = ucfirst($slug[0]);
		if (!class_exists($this->controller)) {
			$this->controller = 'Application';
		} else {
			$slug = array_slice($slug,1);
		}
		return $slug;
	}
	
	private function setAction($slug) {
		$this->action = $slug[0];
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
	
	private function __construct() {
		$this->host = $_SERVER['HTTP_HOST'];
		$this->uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		$slug = explode('/', $_GET['app-slug']);
		$slug = $this->setController($slug);
		$this->params = $this->setAction($slug);
		$this->request['GET'] = array_slice($_GET,1);
		$this->request['POST'] = $_POST;
	}
	
	function __get($name) {
		return (in_array($name, array('request', 'controller', 'action', 'renderArgs', 'requestMethod', 'host', 'uri', 'config'))) ? $this->$name : null;
	}
	
	function __set($name, $value) {
		if ($name == 'renderArgs' && is_array($value)) {
			$this->renderArgs = $value;
		}
	}
	
	function setRenderArg($name, $value) {
		$this->renderArgs[$name] = $value;
	}
		
	function run($config='config.json') {
		$this->config = json_decode(file_get_contents($config));
		call_user_func_array($this->controller . '::beforeAction', $this->params);
		call_user_func_array($this->controller . '::' . $this->action, $this->params);
		call_user_func_array($this->controller . '::afterAction', $this->params);
	}
	
}