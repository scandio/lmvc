<?php

abstract class Controller {

	private static function prepareRenderArgs($renderArgs) {
		App::get()->renderArgs = array_merge(App::get()->renderArgs, $renderArgs);
	}
	
	public static function renderJson($renderArgs=null) {
		self::prepareRenderArgs($renderArgs);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1964 07:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode(App::get()->renderArgs);
	}
	
	public static function render($renderArgs=array()) {
		self::prepareRenderArgs($renderArgs);
		extract(App::get()->renderArgs);
		$view = 'views/' . strtolower(App::get()->controller) . '/' . strtolower(App::get()->action) . '.html';
        if (!file_exists($view)) {
            $reflection = new ReflectionClass(get_called_class());
            $classFileName = $reflection->getFileName();
            $reducer = 'controller/' . App::get()->controller . '.php';
            // TODO build new view
            $view = 'views/' . strtolower(App::get()->controller) . '/' . strtolower(App::get()->action) . '.html';
        }
		include('views/main.html');
	}
	
	public static function redirect($url) {
		header('Location: http://' . App::get()->host . App::get()->uri . $url);
	}

    public static function preProcess() {

    }

    public static function postProcess() {

    }

}