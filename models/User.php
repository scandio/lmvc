<?php

class User extends Model {
	
	public $id = array('type' => int, 'primary' => true, 'increment' => 'auto');
	public $username = array('type' => string, 'unique' => true, 'notnull' => true);
	public $password = array('type' => 'string', 'notnull' => true);
	public $fullname = array('type' => string);
	
	public $tweets = array('entity' => 'Tweet', 'fetch' => 'LAZY', 'cascade' => 'ALL', 'type' => 'OneToMany', 'mappedBy' => 'user');
	public $followings = array('entity' => 'User', 'fetch' => 'LAZY', 'cascade' => 'DETACH', 'type' => 'OneToMany');

}