<?php

class Administration extends SecureController {

    public static function index() {
        self::renderJson(array('result' => 'ok'));
    }

}