<?php

abstract class AbstractForm {

    private $fields = array();
    private $errors = null;
    private $validator = null;

    public function __construct() {
        $reflection = new ReflectionClass(get_called_class());
        $properties = $reflection->getDefaultProperties();
        foreach ($properties as $property => $propertyValue) {
            if (!empty($propertyValue)) {
                $this->fields[$property] = $propertyValue;
            }
        }
    }

    public function validate($request) {
        foreach ($this->fields as $property => $propertyValue) {
            foreach ($propertyValue as $validationMethod => $message) {
                $this->validator = $validationMethod;
                $this->$validationMethod($property, $request->$property);
            }
        }
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
        $this->errors[$name][$this->validator] = $this->fields[$name][$this->validator];
    }

}