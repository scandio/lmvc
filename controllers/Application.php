<?php

class Application extends Controller {
	
	static function index() {
		self::render(array('name'=>'dich!'));
	}

	static function create() {
		EntityManager::get()->dropTable('User');
		EntityManager::get()->createTable('User');
		EntityManager::get()->dropTable('Tweet');
		EntityManager::get()->createTable('Tweet');
		$user = new User();
		$user->username = 'user1';
		$user->password = 'password1';
		$user->fullname = 'The First User';
		//var_dump($user->save());
		$user2 = new User();
		$user2->username = 'user2';
		$user2->password = 'password2';
		$user2->fullname = 'The Second User';
		//var_dump($user2->save());
		$user = User::find('id = :id', 'id', array('id' => 1));
		//var_dump($user);
		$user = User::find();
		//var_dump($user);
		$user = User::find('byUsernameAndId', null, array('username' => 'user2', 'id' => 2));
		//var_dump($user);
		$tweet = new Tweet();
		$tweet->content = "yeah! it works";
		$tweet->created = "2012-03-06";
		$tweet->save();
		self::render(array('tweet' => $tweet));
	}

}