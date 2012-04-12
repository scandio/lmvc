<?php

class SQLBuilder {

    public static function insert($obj) {
        $fields = $obj->__data;
        unset($fields['id']);
        return "INSERT INTO " . strtolower(get_class($obj)) . " (" . implode(', ', array_keys($fields)) . ") VALUES (" . ':' . implode(', :', array_keys($fields)) . ")";
    }

    public static function update($obj) {
        $fields = $obj->__data;
        unset($fields['id']);
        $setFields = array_map(function($value) { return $value . ' = :' . $value; }, array_keys($fields));
        return "UPDATE " . strtolower(get_class($obj)) . " SET " . implode(', ', $setFields) . " WHERE id = :id";
    }

    public static function delete($classname) {
        return "DELETE FROM " . strtolower($classname) . " WHERE id = :id";
    }

    public static function select($classname, $query, $order, $over=null) {
        return "SELECT " . strtolower($classname) . ".* FROM " . strtolower($classname) . (!is_null($over) ? ', ' . $over : '') . (!is_null($query) ? " WHERE " . $query : "") . (!is_null($order) ? " ORDER BY " . $order :"");
    }

}