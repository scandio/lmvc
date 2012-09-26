<?php

class Form extends AbstractForm {

    public function mandatory($name, $value) {
        if (empty($value)) {
            $this->setError($name);
        }
    }

}