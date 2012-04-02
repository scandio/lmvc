<?php

abstract class Controller {

	static function setRenderArg($name, $value) {
		App::get()->setRenderArg($name, $value);
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
	
	static function redirect($url) {
		header('Location: http://' . App::get()->host . App::get()->uri . $url);
	}
	
}