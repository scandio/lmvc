<?php

class User extends Model {
	
	public $username = array('type' => 'string', 'unique' => true, 'notnull' => true);
	public $password = array('type' => 'string', 'notnull' => true);
	public $fullname = array('type' => 'string');
	
	public $tweets = array('entity' => 'Tweet', 'fetch' => 'LAZY', 'cascade' => 'DETACH', 'type' => 'OneToMany', 'mappedBy' => 'user');
	public $followings = array('entity' => 'User', 'fetch' => 'LAZY', 'cascade' => 'DETACH', 'type' => 'OneToMany');

}