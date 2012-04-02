<?php

abstract class Model {

    public function save() {
        if (is_null($this->id)) {
            $sql = SQLBuilder::insert(get_class($this));
        } else {
            $sql = SQLBuilder::update(get_class($this));
        }
        $stmt = App::get()->db()->prepare($sql);
        $stmt->execute($this->getSaveData());
        $this->id = (is_null($this->id)) ? App::get()->db()->lastInsertId() : $this->id;
        return $this;
    }

    private function getSaveData() {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            if ($property->getName() != 'id') {
                $property->setAccessible(true);
                $result[$property->getName()] = $property->getValue($this);
            }
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
        $sql = SQLBuilder::select(get_called_class(), $query, $order);
        $stmt = App::get()->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function findAll($order=null) {
        return self::find(null, $order);
    }

    public static function findById($id) {
        $result = self::find('id = :id', null, array('id' => $id));
        return $result[0];
    }

}
