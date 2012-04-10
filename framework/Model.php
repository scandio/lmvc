<?php

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
                if (in_array($propertyValue, array(MANY_TO_ONE_RELATION, ONE_TO_MANY_RELATION, ONE_TO_ONE_RELATION, ONE_TO_ONE_INVERSED_RELATION))) {
                    $this->__relations[$property] = $propertyValue;
                }
            }
        }
    }

    public function __get($name) {
        if ($name == '__data') {
            return $this->__data;
        } elseif (array_key_exists($name, $this->__relations)) {
            if ($this->__relations[$name] == MANY_TO_ONE_RELATION || $this->__relations[$name] == ONE_TO_ONE_RELATION) {
                $class = ucfirst($name);
                return $class::findById($this->__data[$name . '_id']);
            } elseif ($this->__relations[$name] == ONE_TO_ONE_INVERSED_RELATION || $this->__relations[$name] == ONE_TO_MANY_RELATION) {
                if($this->__relations[$name] == ONE_TO_MANY_RELATION) {
                    $class = ucfirst(substr($name, 0, strlen($name)-1));
                } else {
                    $class = ucfirst($name);
                }
                return $class::find(strtolower(get_class($this)) . '_id = :id', null, array('id' => $this->__data['id']));
            }
        } else {
            return $this->__data[$name];
        }
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

    public static function find($query=null, $order=null, $params=array()) {
        $result = array();
        $class = get_called_class();
        $sql = SQLBuilder::select($class, $query, $order);
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

    public static function findById($id) {
        $result = self::find('id = :id', null, array('id' => $id));
        return $result[0];
    }

}