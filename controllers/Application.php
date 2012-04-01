<?php

class Application extends Controller {
	
	static function index() {
        $tweets = Tweet::findAll('date desc');
        self::setRenderArg('tweets', $tweets);
        self::render();
	}

	static function create() {
        $tweet = new Tweet();
        $tweet->date = strftime('%Y-%m-%d %H:%M:%S');
        $tweet->content = self::request()->content;
        $tweet->user_id = 2;
        $tweet->save();
		self::redirect('Application::index');
	}

}