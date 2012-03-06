<?php

abstract class Model {

   private $__relTypes = array('onetoone', 'onetomany', 'manytoone', 'manytomany');
   private $__fields;
   private $__relations;

	function __construct() {
		$reflection = new ReflectionClass($this);
      $model = unserialize(strtolower(serialize($reflection->getdefaultProperties())));
      foreach ($model as $key => $value) {
         if (in_array($value['type'], $this->__relTypes)) {
            $this->__relations[$key] = $value;
         } else {
            $this->__fields[$key] = $value;
         }
      }
      foreach (array_keys($model) as $key) {
         $this->$key = null;
      }
	}

	final function __get($name) {
		if ($name == 'fields') {
			return $this->__fields;
      } elseif ($name == 'relations') {
         return $this->__relations;
      } elseif ($name == 'classname') {
         return get_called_class();
		}
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