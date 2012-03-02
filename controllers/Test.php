<?php

class Test extends Controller {
	
	static function index() {
		self::setRenderArg('result', 'ok von index');
		self::renderJson();
	}

	static function tast() {
		self::setRenderArg('result', 'OK');
		self::renderJson();
	}
	
	static function postTast() {
		self::renderJson(self::request('POST'));
	}
	
	static function putTast($id) {
		self::renderJson(array($id, self::request()));
	}
	
	static function wasnlos() {
		self::redirect('Application::ttt');
	}

}