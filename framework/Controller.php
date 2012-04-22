<?php

abstract class Controller {

	private static function prepareRenderArgs($renderArgs) {
		App::get()->renderArgs = array_merge(App::get()->renderArgs, $renderArgs);
	}
	
	public static function renderJson($renderArgs=null) {
        if (is_object($renderArgs)) {
            $renderArgs = array($renderArgs->__data);
        }
		self::prepareRenderArgs($renderArgs);
        foreach (App::get()->renderArgs as $key => $renderArg) {
            if (is_object($renderArg)) {
                $result[$key] = $renderArg->__data;
            } else {
                $result[$key] = $renderArg;
            }
        }
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1964 07:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public static function render($renderArgs=array()) {
		self::prepareRenderArgs($renderArgs);
		extract(App::get()->renderArgs);
		$view = App::get()->config->appPath . 'views/' . App::camelCaseTo(App::get()->controller) . '/' . App::camelCaseTo(App::get()->action) . '.html';
        if (!file_exists($view)) {
            $reflection = new ReflectionClass(get_called_class());
            $classFileName = $reflection->getFileName();
            $reducer = 'controllers/' . strtolower(App::get()->controller) . '.php';
            $module = end(explode('/', substr($classFileName, 0,  strpos($classFileName, $reducer)-1)));
            $view = App::get()->config->modulePath . $module . '/views/' . strtolower(App::get()->controller) . '/' . strtolower(App::get()->action) . '.html';
        }
		include(App::get()->config->appPath . 'views/main.html');
	}
	
    public static function redirect($method) {
        $method = explode('::', $method);
        $controller = ($method[0] == 'Application') ? '/' : '/' . App::camelCaseTo($method[0]);
        $action = ($method[1] == 'index') ? '/' : '/' . App::camelCaseTo($method[1]);
        header('Location: http://' . App::get()->host . App::get()->uri . $controller . (($controller == '/' && $action == '/') ? '' : $action) );
    }

    public static function preProcess() {

    }

    public static function postProcess() {

    }

}