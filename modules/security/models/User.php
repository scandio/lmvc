<?php

class User extends Model {
	
	private $id;
    private $username;
	private $password;
	private $fullname;

    private $tweets = ONE_TO_MANY_RELATION;

    public function __set($name, $value) {
        if ($name == 'password') {
            $value = md5($value);
        }
        parent::__set($name, $value);
    }

    public static function authenticate($username, $password) {
        $result = User::find('username = :username AND password = :password', null, array('username' => $username, 'password' => md5($password)));
        return $result[0];
    }

    public static function getCurrentUser() {
        return User::findById($_SESSION['currentUser']);
    }
}