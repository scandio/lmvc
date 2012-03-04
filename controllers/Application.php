<?php

class Application extends Controller {
	
	static function index() {
		self::render();
	}

	static function create() {
		User::dropTable();
		User::createTable();
		$user = new User();
		$user->username = 'user1';
		$user->password = 'password1';
		$user->fullname = 'The First User';
		$user->save();
		var_dump($user);
	}
	
}