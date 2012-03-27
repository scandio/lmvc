<?php

class ModelManager {

    private static $object=null;
    private $models=array();

    private function __construct() {
        $this->readModels();
    }

    static function get() {
        if(is_null(self::$object)) {
            self::$object = new ModelManager();
        }
        return self::$object;
    }

    function createTable($classname) {
        $sqlBuilder = $this->sqlBuilder;
        $statement = $sqlBuilder::create($this->models[$classname]);
        return $this->pdoConnection->exec($statement);
    }

    function dropTable($classname) {
        $sqlBuilder = $this->sqlBuilder;
        $statement = $sqlBuilder::drop($this->models[$classname]);
        return $this->pdoConnection->exec($statement);
    }

    private function readModels() {
        $directory = opendir('models');
        while($entry = readdir($directory)) {
            if (!$this->isHidden($entry)) {
                $entry = substr($entry, 0, strlen($entry)-4);
                $this->models[$entry] = new $entry();
            }
        }
        var_dump($this);
    }

    private function isHidden($filename) {
        return strncmp($filename, '.', 1) == 0;
    }

}
