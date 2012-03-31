<?php

class SQLBuilder {

    private static function getProperties($classname) {
        $reflection = new ReflectionClass($classname);
        return $reflection->getdefaultProperties();
    }

    static function insert($classname) {
        $fields = self::getProperties($classname);
        foreach ($fields as $key => $field) {
            if ($key != 'id') {
                $insFields[$key] = $field;
            }
        }
        return "INSERT INTO " . strtolower($classname) . " (" . implode(', ', array_keys($insFields)) . ") VALUES (" . ':' . implode(', :', array_keys($insFields)) . ")";
    }

    static function update($classname) {
        $fields = self::getProperties($classname);
        $setFields = array_map(function($value) { return $value . ' = :' . $value; }, array_keys($fields));
        return "UPDATE " . strtolower($classname) . " SET " . implode(', ', $setFields) . " WHERE id = :id";
    }

    static function delete($classname) {
        return "DELETE FROM " . strtolower($classname) . " WHERE id = :id";
    }

    static function select($classname, $query, $order) {
        return "SELECT * FROM " . strtolower($classname) . (!is_null($query) ? " WHERE " . $query : "") . (!is_null($order) ? " ORDER BY " . $order :"");
    }

}