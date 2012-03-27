<?php

class Application extends Controller {
	
	static function index() {
		self::render(array('name'=>'dich!'));
	}

	static function create() {
		self::render();
	}

}