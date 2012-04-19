<?php

class SQLBuilder {

    private static $log = true;

    private static function log($value) {
        if (self::$log) {
            error_log($value."\n", 3, 'sql.log');
        }
        return $value;
    }

    public static function insert($obj) {
        $fields = $obj->__data;
        unset($fields['id']);
        return self::log("INSERT INTO " . strtolower(get_class($obj)) . " (" . implode(', ', array_keys($fields)) . ") VALUES (" . ':' . implode(', :', array_keys($fields)) . ")");
    }

    public static function insertRelation($fromClass, $toClass, $relationType) {
        $overTable = strtolower(($relationType == MANY_TO_MANY_INVERSED_RELATION) ? $toClass . '_' . $fromClass : $fromClass . '_' . $toClass);
        return self::log("INSERT INTO {$overTable} ( " . strtolower($fromClass) . "_id, " . strtolower($toClass) . "_id ) VALUES ( ?, ? )");
    }

    public static function update($obj) {
        $fields = $obj->__data;
        unset($fields['id']);
        $setFields = array_map(function($value) { return $value . ' = :' . $value; }, array_keys($fields));
        return self::log("UPDATE " . strtolower(get_class($obj)) . " SET " . implode(', ', $setFields) . " WHERE id = :id");
    }

    public static function delete($classname) {
        return self::log("DELETE FROM " . strtolower($classname) . " WHERE id = :id");
    }

    public static function select($classname, $query, $order, $over=null) {
        return self::log("SELECT " . strtolower($classname) . ".* FROM " . strtolower($classname) . (!is_null($over) ? ', ' . $over : '') . (!is_null($query) ? " WHERE " . $query : "") . (!is_null($order) ? " ORDER BY " . $order :""));
    }

}