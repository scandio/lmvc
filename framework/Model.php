<?php

abstract class Model {

	private $__model;

	function __construct() {
		$reflection = new ReflectionClass($this);
        $this->__model = unserialize(strtolower(serialize($reflection->getdefaultProperties())));
        foreach (array_keys($this->__model) as $key) {
           $this->$key = null;
        }
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
   		$result = EntityManager::get()->save($this);
         $this->id = $result->id;
   	}

   	function delete() {
   		EntityManager::get()->delete($this);
   	}

   	static function find($query=null, $order=null, $params=null) {
   		return EntityManager::get()->find($query, $order, $params, get_called_class());
   	}

   	static function findById($id) {
   		return self::find('byId', null,  array('id' => $id));
   	}

      static function createTable() {
         return EntityManager::get()->createTable(get_called_class());
      }

      static function dropTable() {
         return EntityManager::get()->dropTable(get_called_class());
      }

}