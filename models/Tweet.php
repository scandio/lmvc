<?php

class Tweet extends Model {
	
	public $created = array('type' => 'datetime', 'notnull' => true);
	public $content = array('type' => 'string', 'notnull' => true);
	
	public $user = array('entity' => 'User', 'fetch' => 'LAZY', 'cascade' => 'DETACH', 'type' => 'ManyToOne');

}