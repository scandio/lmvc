<?php

abstract class Controller {

    public static function setRenderArg($name, $value) {
        App::get()->setRenderArg($name, $value);
    }

    public static function setRenderArgs($renderArgs, $add=false) {
        App::get()->renderArgs = ($add) ? array_merge(App::get()->renderArgs, $renderArgs) : $renderArgs;
    }

	public static function renderJson($renderArgs=null, ArrayBuilder $arrayBuilder=null) {
        if (is_object($renderArgs) && $arrayBuilder instanceof ArrayBuilder) {
            $renderArgs = $arrayBuilder::build($renderArgs);
        }
		self::setRenderArgs($renderArgs, true);
        foreach (App::get()->renderArgs as $key => $renderArg) {
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
	
	public static function render($renderArgs=array()) {
		self::setRenderArgs($renderArgs, true);
		extract(App::get()->renderArgs);
        $app = App::get();
		$app->view = $app->config->appPath . 'views/' . App::camelCaseTo(App::get()->controller) . '/' . App::camelCaseTo(App::get()->action) . '.html';
        if (!file_exists($app->view)) {
            $reflection = new ReflectionClass(get_called_class());
            $classFileName = $reflection->getFileName();
            $reducer = 'controllers/' . strtolower(App::get()->controller) . '.php';
            $module = end(explode('/', substr($classFileName, 0,  strpos($classFileName, $reducer)-1)));
            $app->view = $app->config->modulePath . $module . '/views/' . strtolower($app->controller) . '/' . strtolower($app->action) . '.html';
        }
		include($app->config->appPath . 'views/main.html');
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