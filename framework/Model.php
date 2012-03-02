<?php

abstract class Model {

	private $__model;

	function __construct() {
		$reflection = new ReflectionClass($this);
        $this->__model = unserialize(strtolower(serialize($reflection->getdefaultProperties())));
   	}

   	final function __get($name) {
   		if ($name == 'model') {
   			return $this->__model;
   		} elseif ($name == 'classname') {
   			return get_called_class();
   		}
   	}

   	function __call($name, $params) {
   		return empty($params) ? $this->__model[$name] : $this->__model[$name][$params[0]];
   	}

   	function save() {
   		EntityManager::save($this);
   	}

   	function delete() {
   		EntityManager::delete($this);
   	}

   	static function find($query, $params) {
   		EntityManager::find($query, $params, get_called_class());
   	}

   	static function findById($id) {
   		return self::find('byId', array($id));
   	}

}