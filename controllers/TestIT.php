<?php

class TestIt extends Controller {

    public static function index() {
        self::redirect('TestIt::testItNow');
    }

    public static function testItNow() {
        self::renderJson(array('result'=>'OK', 'controller' => App::get()->controller, 'action' => App::get()->action));
    }

}