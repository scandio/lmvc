<?php

class Relation extends ArrayObject {

    private $fromObj;
    private $toClass;
    private $relationType;
    private $forSave=array();

    public function __construct($relationType, $fromObj, $toClass, $objectList) {
        $this->relationType = $relationType;
        $this->fromObj = $fromObj;
        $this->toClass = $toClass;
        foreach ($objectList as $obj) {
            if ($this->toClass == get_class($obj)) {
                parent::append($obj);
            }
        }
    }

    public function add($obj) {
        if ($this->toClass == get_class($obj)) {
            parent::append($obj);
            $this->forSave[$obj->id] = $obj;
            return true;
        }
        return false;
    }

    public function save() {
        foreach ($this->forSave as $key => $obj) {
            if ($this->relationType == MANY_TO_MANY_RELATION || $this->relationType == MANY_TO_MANY_INVERSED_RELATION) {
                $sql = SQLBuilder::insertRelation(get_class($this->fromObj), $this->toClass, $this->relationType);
                $stmt = App::get()->db()->prepare($sql);
                $stmt->execute(array($this->fromObj->id, $obj->id));
            }
        }
    }

}
