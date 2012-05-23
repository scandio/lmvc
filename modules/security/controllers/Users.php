<?php

class Users extends SecureController {

	public static function index() {
		self::render(array('users' => User::findAll('username')));
	}

	public static function create() {
		self::render();
	}

	public static function postCreate() {
		self::fillSave(new User());
	}

	private static function fillSave($user) {
		$user->username = LVC::get()->request->username;
		$user->password = LVC::get()->request->password;
		$user->fullname = LVC::get()->request->fullname;
		$user->save();
		self::redirect('Users::index');
	}

	public static function delete($id) {
		$user = User::findById($id);
		$user->delete();
		self::redirect('Users::index');
	}

	public static function edit($id) {
		self::render(array('user' => User::findById($id)));		
	}

	public static function postEdit($id) {
		self::fillSave(User::findById($id));
	}

}