<?php

class EntityManager {

	private static $object = null;
	private $pdoConnection;
	private $sqlBuilder;

	static function get() {
		if (is_null(self::$object)) {
			self::$object = new EntityManager();
		}
		return self::$object;
	}

	private function fieldData($entity) {
		foreach ($entity->__fields as $key => $value) {
			if($key != 'id') {
				$fieldData[$key] = $entity->$key;
			}
		}
		return $fieldData;
	}

	function save($entity) {
		$sqlBuilder = $this->sqlBuilder;
		if (is_null($entity->id)) {
			$statement = $sqlBuilder::insert($entity);
		} else {
			$statement = $sqlBuilder::update($entity);
		}
		$pdoStatement = $this->pdoConnection->prepare($statement);
		$pdoStatement->execute($this->fieldData($entity));
		$entity->id = (is_null($entity->id)) ? $this->pdoConnection->lastInsertId() : $entity->id;
		return $entity;
	}

	function delete($entity) {
		$sqlBuilder = $this->sqlBuilder;
		$statement = $sqlBuilder::delete($entity);
		$pdoStatement = $this->pdoConnection->prepare($statement);
		return $pdoStatement->execute(array('id' => $entity->id));
	}

	function find($query=null, $order=null, $params=null, $classname) {
		$entity = new $classname();
		$sqlBuilder = $this->sqlBuilder;
		$statement = $sqlBuilder::select($entity, $query, $order, $params);
		$pdoStatement = $this->pdoConnection->prepare($statement);
		$pdoStatement->execute($params);
		return $pdoStatement->fetchAll();
	}

	private function __construct() {
		$this->pdoConnection = new PDO(App::get()->config->dsn, App::get()->config->user, App::get()->config->password);
		$this->sqlBuilder = App::get()->config->sqlBuilder;
		$this->pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

}