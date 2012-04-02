<?php

class Application extends Controller {
	
	static function index() {
        $tweets = Tweet::findAll('date desc');
        self::setRenderArg('tweets', $tweets);
        self::render();
	}

    static function getAll() {
        $tweets = Tweet::findAll('date desc');
        self::renderJson(array('tweets' => $tweets));
    }

    static function createUser() {
        $user = new User();
        $user->username = 'test2';
        $user->password = 'okok';
        $user->email = 'no';
        $user->save();
        $x = User::authenticate('test2', 'okok');
        var_dump($x->id);
    }

	static function create() {
        $tweet = new Tweet();
        $tweet->date = strftime('%Y-%m-%d %H:%M:%S');
        $tweet->content = self::request()->content;
        $tweet->user_id = 2;
        $tweet->save();
		self::redirect('/');
	}

}