<?php

class Application extends Controller {
	
	public static function index() {
        $tweets = Tweet::findAll('date desc');
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
        $tweet->user_id = 2;
        $tweet->save();
        self::redirect('/');
    }

    public static function login() {
        self::render();
    }

    public static function postLogin() {
        $user = User::authenticate(App::get()->request->username, App::get()->request->password);
        if (is_object($user)) {
            $_SESSION['currentUser'] = $user->id;
            self::redirect('/users');
        } else {
            $_SESSION['currentUser'] = null;
            self::redirect('/login');
        }
    }

}