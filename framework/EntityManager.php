<?php

class EntityManager {

	private static $object=null;
	private $pdoConnection=null;

	private static function get() {
		if (is_null(self::$object)) {
			self::$object = new EntityManager();
		}
		return self::$object;
	}

	static function save($entity) {
		var_dump($entity->classname, $entity->model);
	}

	static function delete($entity) {

	}

	static function find($query, $params, $classname) {
		var_dump($query, $params, $classname);
	}

	private function __construct() {
		if (is_null($this->pdoConnection)) {
			$this->pdoConnection = new PDO(App::get()->config->dsn, App::get()->config->user, App::get()->config->password);
		}
	}
	
}