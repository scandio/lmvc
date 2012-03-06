<?php

class Tweet extends Model {
	
	public $id = array('type' => int, 'primary' => true, 'increment' => 'auto');
	public $created = array('type' => datetime, 'notnull' => true);
	public $content = array('type' => string, 'notnull' => true);
	
	public $user = array('entity' => 'User', 'fetch' => 'LAZY', 'cascade' => 'DETACH', 'type' => 'ManyToOne');

}