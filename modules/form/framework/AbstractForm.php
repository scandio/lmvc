<?php

abstract class AbstractForm {

    private $__formdata = null;

    public function __construct() {
        $this->__formdata = new stdClass();
        $reflection = new ReflectionClass(get_class($this));
        $this->__formdata->fields = $reflection->getDefaultProperties();
        unset($this->__formdata->fields['__formdata']);
    }

    public function validate($request, $params=array()) {
        $this->__formdata->request = $request;
        $this->__formdata->params = $params;
        foreach ($this->__formdata->fields as $property => $propertyValue) {
            foreach ($propertyValue as $validator => $params) {
                $this->__formdata->validator = $validator;
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
        return $this->__formdata->errors;
    }

    public function isValid() {
        return !$this->hasErrors();
    }

    public function hasErrors() {
        return is_array($this->__formdata->errors);
    }

    public function setError($name, $params=array(), $message=null) {
        if (empty($message)) {
            $message = $this->__formdata->fields[$name][$this->__formdata->validator]['message'];
        }
        if (count($params) > 0) {
            $message = call_user_func_array('sprintf', array_merge((array)$message, $params));
        }
        $this->__formdata->errors[$name][$this->__formdata->validator] = $message;
    }

    protected function request() {
        return $this->__formdata->request;
    }

    protected function params() {
        return $this->__formdata->params;
    }

}