<?php

abstract class SecureController extends Controller {

    public static function preProcess() {
        $user = $_SESSION['currentUser'];
        if (is_null($user)) {
            $_SESSION['afterLogin'] = '/' . strtolower(App::get()->controller) . '/' . App::get()->action;
            self::redirect('/security/login');
        }
    }

    public static function postProcess() {

    }

}