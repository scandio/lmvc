<?php

class User extends Model {
	
	private $id = array('type' => int, 'primary' => true, 'increment' => 'auto');
	private $username = array('type' => string, 'unique' => true, 'notnull' => true);
	private $password = array('type' => 'password', 'notnull' => true);
	private $fullname = array('type' => string, 'notnull' => true);
	
	private $articles = array('entity' => 'Article', 'fetch' => 'LAZY', 'cascade' => 'ALL', 'type' => 'OneToMany', 'mappedBy' => 'user');

}