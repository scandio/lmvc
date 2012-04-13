<?php

class Relation extends ArrayObject {

    private $class;
    private $relationType;

    public function __construct($relationType, $class, $objectList) {
        $this->relationType = $relationType;
        $this->class = $class;
        foreach ($objectList as $obj) {
            $this->add($obj);
        }
    }

    public function add($obj) {
        $class = get_class($obj);
        if ($this->class == $class) {
            parent::append($obj);
            $result = true;
        }
        return ($result) ? true : false;
    }

}
