<?php

abstract class Controller {
	
	static function setRenderArg($name, $value) {
		App::get()->setRenderArg($name, $value);
	}
	
	static function request($requestMethod=null) {
		return (object) App::get()->request;
	}
	
	private static function prepareRenderArgs($renderArgs) {
		App::get()->renderArgs = array_merge(App::get()->renderArgs, $renderArgs);
	}
	
	static function renderJson($renderArgs=null) {
		self::prepareRenderArgs($renderArgs);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1964 07:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode(App::get()->renderArgs);
	}
	
	static function render($renderArgs=array()) {
		self::prepareRenderArgs($renderArgs);
		extract(App::get()->renderArgs);
		$view = 'views/' . strtolower(App::get()->controller) . '/' . strtolower(App::get()->action) . '.html';
		include('views/main.html');
	}
	
	static function redirect($method) {
		$method = explode('::', $method);
		$controller = ($method[0] == 'Application') ? '/' : '/' . strtolower($method[0]);
		$action = ($method[1] == 'index') ? '' : '/' . $method[1];
		header('Location: http://' . App::get()->host . App::get()->uri . $controller . $action );
		exit;
	}
	
}