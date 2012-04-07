<?php

class Application extends SecureController {
	
	public static function index() {
        $tweets = Tweet::findAll('date desc');
        $currentUser = User::getCurrentUser();
        App::get()->setRenderArg('currentUser', $currentUser);
        App::get()->setRenderArg('tweets', $tweets);
        self::render();
	}

    public static function getAll() {
        $tweets = Tweet::findAll('date desc');
        self::renderJson(array('tweets' => $tweets));
    }

    public static function create() {
        $tweet = new Tweet();
        $tweet->date = strftime('%Y-%m-%d %H:%M:%S');
        $tweet->content = App::get()->request->content;
        $tweet->user_id = User::getCurrentUser()->id;
        $tweet->save();
        self::redirect('Application::index');
    }

}