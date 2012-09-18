<?php

abstract class Controller {

    public static function setRenderArg($name, $value) {
        LVC::get()->setRenderArg($name, $value);
    }

    public static function setRenderArgs($renderArgs, $add=false) {
        if (!is_null($renderArgs)) {
            LVC::get()->renderArgs = ($add) ? array_merge(LVC::get()->renderArgs, $renderArgs) : $renderArgs;
        }
    }

    public static function renderJson($renderArgs=null, ArrayBuilder $arrayBuilder=null) {
        if (is_object($renderArgs) && $arrayBuilder instanceof ArrayBuilder) {
            $renderArgs = $arrayBuilder::build($renderArgs);
        }
        self::setRenderArgs($renderArgs, true);
        foreach (LVC::get()->renderArgs as $key => $renderArg) {
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

    public static function render($renderArgs=array(), $masterTemplate=null) {
        self::setRenderArgs($renderArgs, true);
        $args = LVC::get()->renderArgs;
        extract($args);
        unset($args);
        $app = LVC::get();
        $app->view = $app->config->appPath . 'views/' . LVC::camelCaseTo($app->controller) . '/' . LVC::camelCaseTo($app->action) . '.html';
        if (!file_exists($app->view)) {
            $reflection = new ReflectionClass(get_called_class());
            $classFileName = $reflection->getFileName();
            $reducer = 'controllers/' . $app->controller . '.php';
            $module = end(explode('/', substr($classFileName, 0,  strpos($classFileName, $reducer)-1)));
            $app->view = $app->config->modulePath . $module . '/views/' . strtolower($app->controller) . '/' . strtolower($app->action) . '.html';
        }
        if (!is_null($masterTemplate)) {
            include($masterTemplate);
        } else {
            include($app->config->appPath . 'views/main.html');
        }
    }

    public static function redirect($method, $params=null) {
        header('Location: ' . LVC::get()->url($method, $params));
    }

    public static function preProcess() {

    }

    public static function postProcess() {

    }

}