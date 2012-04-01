<?php

abstract class Model {

    private $id=null;

    function __get($name) {
        if ($name == 'id') {
            return $this->id;
        }
    }

    function save() {
        if (is_null($this->id)) {
            $sql = SQLBuilder::insert(get_class($this));
            $stmt = App::get()->db()->prepare($sql);
            $stmt->execute($this->getSaveData(true));
            $this->id = App::get()->db()->lastInsertId();
        } else {
            $sql = SQLBuilder::update(get_class($this));
            $stmt = App::get()->db()->prepare($sql);
            $stmt->execute($this->getSaveData());
        }
        return $this;
    }

    private function getSaveData($forInsert=false) {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getdefaultProperties();
        foreach ($properties as $key => $property) {
            if (($forInsert && $key != 'id') || !$forInsert) {
                $result[$key] = $this->$key;
            }
        }
        return $result;
    }

    function delete() {
        if(!is_null($this->id)) {
            $sql = SQLBuilder::delete(get_class($this));
            $stmt = App::get()->db()->prepare($sql);
            $stmt->execute(array('id' => $this->id));
        }
    }

    static function find($query=null, $order=null, $params=array()) {
        $sql = SQLBuilder::select(get_called_class(), $query, $order);
        $stmt = App::get()->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    static function findAll($order=null) {
        return self::find(null, $order);
    }

    static function findById($id) {
        $result = self::find('id = :id', null, array('id' => $id));
        return $result[0];
    }

}
