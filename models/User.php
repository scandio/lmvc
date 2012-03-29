<?php

$user = new User();
$user->username = 'ckoch';

define('ONE_TO_MANY_RELATION', 'ONE_TO_MANY_RELATION');

class User extends Model {

    private $id;
    public $username;
    public $passowrd;
    public $fullname;

    public $tweets = ONE_TO_MANY_RELATION;

}

class Model {

    function save() {

    }

    function delete() {

    }

    static function find() {

    }

}