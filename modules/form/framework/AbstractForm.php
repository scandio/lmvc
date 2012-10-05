<?php

abstract class AbstractForm {

    private $fields = array();
    private $errors = null;
    private $validator = null;
    protected $request = null;
    protected $params = array();


    public function __construct() {
        $reflection = new ReflectionClass(get_class($this));
        $this->fields = $reflection->getDefaultProperties();
    }

    public function validate($request, $params=array()) {
        $this->request = $request;
        $this->params = $params;
        foreach ($this->fields as $property => $propertyValue) {
            foreach ($propertyValue as $validator => $params) {
                $this->validator = $validator;
                $validationInfo = $this->getValidationInfo($validator);
                if (!is_null($validationInfo)) {
                    $camelCasedMethod = $validationInfo['method'];
                    $this->$camelCasedMethod($property, $validationInfo['rule']);
                }
            }
        }
    }

    private function getValidationInfo($validator) {

        $newValidator = $validator;
        $rule = array();
        while (!method_exists($this, LVC::camelCaseFrom($newValidator))) {
            $dashPos = strrpos($newValidator, '-');
            if ($dashPos > 0) {
                $rule[] = substr($newValidator, $dashPos + 1);
                $newValidator = substr($newValidator, 0, $dashPos);
            } else {
                error_log('LMVC -- Undefined validator in ' . get_class($this));
                return null;
            }
        }
        return array(
            'method' => LVC::camelCaseFrom($newValidator),
            'rule' => implode('-', array_reverse($rule))
        );
    }

    public function getErrors() {
        return $this->errors;
    }

    public function isValid() {
        return !$this->hasErrors();
    }

    public function hasErrors() {
        return is_array($this->errors);
    }

    public function setError($name) {
        $this->errors[$name][$this->validator] = $this->fields[$name][$this->validator]['message'];
    }

}