<?php

define('MANY_TO_ONE_RELATION', 'MANY_TO_ONE_RELATION');
define('ONE_TO_MANY_RELATION', 'ONE_TO_MANY_RELATION');
define('ONE_TO_ONE_RELATION', 'ONE_TO_ONE_RELATION');
define('ONE_TO_ONE_INVERSED_RELATION', 'ONE_TO_ONE_INVERSED_RELATION');
define('MANY_TO_MANY_RELATION', 'MANY_TO_MANY_RELATION');
define('MANY_TO_MANY_INVERSED_RELATION', 'MANY_TO_MANY_INVERSED_RELATION');

abstract class Model {

    private $__data=array();
    private $__fields=array();
    private $__relations=array();

    public function __construct() {
        $reflection = new ReflectionClass(get_called_class());
        $properties = $reflection->getDefaultProperties();
        foreach ($properties as $property => $propertyValue) {
            if (empty($propertyValue)) {
                $this->__fields[$property] = null;
            } else {
                if (in_array($propertyValue, array(MANY_TO_ONE_RELATION, ONE_TO_MANY_RELATION, ONE_TO_ONE_RELATION, ONE_TO_ONE_INVERSED_RELATION, MANY_TO_MANY_RELATION, MANY_TO_MANY_INVERSED_RELATION))) {
                    $this->__relations[$property] = $propertyValue;
                }
            }
        }
    }

    public function __get($name) {
        if ($name == '__data') {
            $result = $this->__data;
        } elseif (array_key_exists($name, $this->__relations)) {
            if ($this->__relations[$name] == MANY_TO_ONE_RELATION || $this->__relations[$name] == ONE_TO_ONE_RELATION) {
                $class = ucfirst($name);
                $result = $class::findById($this->__data[$name . '_id']);
            } elseif ($this->__relations[$name] == ONE_TO_ONE_INVERSED_RELATION) {
                $class = ucfirst($name);
                $result = $class::findFirst(strtolower(get_class($this)) . '_id = :id', null, array('id' => $this->__data['id']));
            } elseif ($this->__relations[$name] == ONE_TO_MANY_RELATION) {
                $class = ucfirst(substr($name, 0, strlen($name)-1));
                $result = $class::find(strtolower(get_class($this)) . '_id = :id', null, array('id' => $this->__data['id']));
            } elseif ($this->__relations[$name] == MANY_TO_MANY_RELATION || $this->__relations[$name] == MANY_TO_MANY_INVERSED_RELATION) {
                $local = strtolower(get_class($this));
                $remote = substr($name, 0, strlen($name)-1);
                $overTable = ($this->__relations[$name] == MANY_TO_MANY_INVERSED_RELATION) ? $remote . '_' . $local : $local . '_' . $remote;
                $class = ucfirst($remote);
                $result = $class::find("{$overTable}.{$local}_id = :id AND {$overTable}.{$remote}_id = {$remote}.id",null, array('id' => $this->__data['id']), $overTable);
            }
        }
        return ($result) ? $result : $this->__data[$name];
    }

    public function __set($name, $value) {
        if ($name == '__data') {
            $this->__data = $value;
        }elseif (array_key_exists($name, $this->__relations)) {
            if (is_object($value) && ($this->__relations[$name] == MANY_TO_ONE_RELATION || $this->__relations[$name] == ONE_TO_ONE_RELATION )) {
                $this->__data[$name . '_id'] = $value->id;
            }
        } else {
            $this->__data[$name] = $value;
        }
    }

    public function save() {
        if (is_null($this->id)) {
            $sql = SQLBuilder::insert($this);
            $stmt = App::get()->db()->prepare($sql);
            $stmt->execute($this->getSaveData(true));
            $this->id = App::get()->db()->lastInsertId();
        } else {
            $sql = SQLBuilder::update($this);
            $stmt = App::get()->db()->prepare($sql);
            $stmt->execute($this->getSaveData());
        }
        return $this;
    }

    private function getSaveData($forInsert=false) {
        $result = $this->__data;
        if ($forInsert) {
            unset($result['id']);
        }
        return $result;
    }

    public function delete() {
        if(!is_null($this->id)) {
            $sql = SQLBuilder::delete(get_class($this));
            $stmt = App::get()->db()->prepare($sql);
            $stmt->execute(array('id' => $this->id));
        }
    }

    public static function find($query=null, $order=null, $params=array(), $over=null) {
        $result = array();
        $class = get_called_class();
        $sql = SQLBuilder::select($class, $query, $order, $over);
        $stmt = App::get()->db()->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($records as $data) {
            $obj = new $class();
            $obj->__data = $data;
            $result[] = $obj;
        }
        return $result;
    }

    public static function findAll($order=null) {
        return self::find(null, $order);
    }

    public static function findFirst($query=null, $order=null, $params=array()) {
        $result = self::find($query, $order, $params);
        return $result[0];
    }

    public static function findById($id) {
        return self::findFirst('id = :id', null, array('id' => $id));
    }

}