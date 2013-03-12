<?php

namespace lmvc\modules\form\framework;

class Form extends AbstractForm {

    public function mandatory($name) {
        if (strlen(trim($this->request()->$name)) == 0) {
            $this->setError($name);
        }
    }

}