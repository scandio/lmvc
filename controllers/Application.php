<?php

class Application extends Controller {
	
	static function index() {
		self::render();
	}

	static function abc() {
		$user = User::findById(1);
		$user = new User();
		$user->save();
		//self::render($user);
	}
	
}