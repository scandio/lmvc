<?php

class Application extends SecureController {
	
	public static function index() {
        $tweets = Tweet::findAll('date desc');
        $currentUser = User::getCurrentUser();
        self::setRenderArg('currentUser', $currentUser);
        self::setRenderArg('tweets', $tweets);
        self::render();
	}

    public static function getAll() {
        $tweets = Tweet::findAll('date desc');
        self::renderJson(array('tweets' => $tweets));
    }

    public static function create() {
        $tweet = new Tweet();
        $tweet->date = strftime('%Y-%m-%d %H:%M:%S');
        $tweet->content = LVC::get()->request->content;
        $tweet->user = User::getCurrentUser();
        $tweet->save();
        self::redirect('Application::index');
    }

}