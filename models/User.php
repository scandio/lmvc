<?php

class User extends Model {
	
	public $id;
    public $username;
	private $password;
	public $fullname;

    public function __get($name) {
        if ($name == 'password') {
            return $this->password;
        }
    }

    public function __set($name, $value) {
        if ($name == 'password') {
            $this->password = md5($value);
        }
    }

    public static function authenticate($username, $password) {
        return User::find('username = :username AND password = :password', null, array('username' => $username, 'password' => md5($password)));
    }
}