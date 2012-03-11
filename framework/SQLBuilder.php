<?php

class SQLBuilder {

	static function drop($entity) {
		return "DROP TABLE " . self::underScored($entity->__classname);
	}

	static function create($entity) {
		foreach ($entity->__fields as $key => $value) {
			$fields[] = $key . ' ' . self::buildFieldType($value); 
		}
		return "CREATE TABLE " . self::underScored($entity->__classname) . " (" . implode(', ', $fields) . ")";
	}

	private static function buildFieldType($field) {
		if ($field['type'] == 'string') {
			$type ='TEXT';
		} elseif ($field['type'] == 'int') {
			$type = 'INTEGER';
		} elseif ($field['type'] == 'datetime') {
			$type = 'TEXT';
		}
		if ($field['notnull'] == true) {
			$type .= ' NOT NULL';  
		}
		if ($field['unique'] == true) {
			$type .= ' UNIQUE';
		}
		if ($field['primary'] == true) {
			$type .= ' PRIMARY KEY';
		}
		if ($field['increment'] == 'auto') {
			$type .= ' AUTOINCREMENT';
		}
		return $type;
	}
	
	private static function fieldFilter($field) {
		return !(array_key_exists('increment', $field) && $field['increment'] == 'auto');
	}

	static function insert($entity) {
		$fields = array_filter($entity->__fields, 'SQLBuilder::fieldFilter');
		$fieldsParams = array_map(function($value) { return ':' . $value; }, array_keys($fields));
		return "INSERT INTO " . self::underScored($entity->__classname) . " (" . implode(', ', array_keys($fields)) . ") VALUES (" . implode(', ', $fieldsParams) . ")";
	}

	static function update($entity) {
		$fields = array_filter($entity->__fields, 'SQLBuilder::fieldFilter');
		$setFields = array_map(function($value) { return $value . ' = :' . $value; }, array_keys($fields));
		return "UPDATE " . self::underScored($entity->__classname) . " SET " . $setFields . " WHERE id = :id";
	}

	static function delete($entity) {
		return "DELETE FROM " . self::underScored($entity->__classname) . " WHERE id = :id";
	}

	static function select($entity, $query=null, $order=null, $params=null) {
		$queryArray = explode('_', self::underScored($query));
		$result = '';
		if ($queryArray[0] == 'by') {
			for ($i = 1; $i < count($queryArray); $i++) { 
				if($i % 2 == 1) {
					$result .= $queryArray[$i] . ' =  :' . $queryArray[$i];
				} else {
					$result .= ' ' . strtoupper($queryArray[$i]) . ' ';
				}
			}
			$query = $result;
		}
		return "SELECT * FROM " . self::underScored($entity->__classname) . (!is_null($query) ? " WHERE " . $query : "") . (!is_null($order) ? " ORDER BY " . $order :"");
	}

	private static function underScored($camelCased) {
		return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $camelCased)); 
	}

}