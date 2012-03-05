<?php

class EntityManager {

	private static $object = null;
	private $pdoConnection;

	static function get() {
		if (is_null(self::$object)) {
			self::$object = new EntityManager();
		}
		return self::$object;
	}

	function save($entity) {
		$sqlBuilder = new SQLBuilder($entity->model, $entity->classname, $this->pdoConnection);
		if (is_null($entity->id)) {
			$entity = $sqlBuilder->insert($entity);
		} else {
			$entity = $sqlBuilder->update($entity);
		}
		return $entity;
	}

	function delete($entity) {
		$sqlBuilder = new SQLBuilder($entity->model, $entity->classname, $this->pdoConnection);
		return $sqlBuilder->delete($entity);
	}

	function find($query=null, $order=null, $params=null, $classname) {
		$entity = new $classname();
		$sqlBuilder = new SQLBuilder($entity->model, $entity->classname, $this->pdoConnection);		
		return $sqlBuilder->select($query, $order, $params);
	}

	private function __construct() {
		$this->pdoConnection = new PDO(App::get()->config->dsn, App::get()->config->user, App::get()->config->password);
		$this->pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	}

	function createTable($classname) {
		$entity = new $classname();
		$sqlBuilder = new SQLBuilder($entity->model, $entity->classname, $this->pdoConnection);
		$sqlBuilder->create();		
	}

	function dropTable($classname) {
		$entity = new $classname();
		$sqlBuilder = new SQLBuilder($entity->model, $entity->classname, $this->pdoConnection);
		$sqlBuilder->drop();
	}
	
}