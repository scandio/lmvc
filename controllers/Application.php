<?php

class Application extends Controller {
	
	static function index() {
        $tweets = Tweet::find(null, 'date');
        self::setRenderArg('tweets', $tweets);
        self::render();
	}

	static function create() {
		self::render();
	}

}