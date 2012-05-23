<?php

class TestIt extends Controller {

    public static function index() {
        self::redirect('TestIt::testItNow');
    }

    public static function testItNow() {
        self::renderJson(array('result'=>'OK', 'controller' => LVC::get()->controller, 'action' => LVC::get()->action));
    }

}