<?php

abstract class SecureController extends Controller {

    public static function preProcess() {
        $user = $_SESSION['currentUser'];
        if (is_null($user)) {
            self::redirect('/login');
        }
    }

    public static function postProcess() {

    }

}