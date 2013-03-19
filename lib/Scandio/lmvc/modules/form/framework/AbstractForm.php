<?php

namespace Scandio\lmvc\modules\form\framework;

use \Scandio\lmvc\framework\LVC;

/**
 * Form Validator class
 * Create a class extending from AbstractForm and define the form fields as
 * public members. Write your own validator methods and use their camelCased
 * validator string as method name.
 *
 * class MyForm extends AbstractForm {
 *
 *      public myField = array(
 *          'my-validator' = array(
 *              'message' = 'my error message for %s'
 *          )
 *      );
 *
 *      public function myValidator($name) {
 *          if ($this->request->$name != 'LVC') {
 *              $this->setError($name);
 *          }
 *      }
 * }
 *
 * In an action you can use it like validate(self::request());
 */
abstract class AbstractForm {

    /**
     * @var null|stdClass hidden member for internal use
     */
    private $__formdata = null;

    /**
     * reads the form members and fills __formdata
     */
    public function __construct() {
        $this->__formdata = new stdClass();
        $reflection = new ReflectionClass(get_class($this));
        $this->__formdata->fields = $reflection->getDefaultProperties();
        unset($this->__formdata->fields['__formdata']);
    }

    /**
     * validates the whole form from the request array
     *
     * @param $request the form request from LVC
     * @param array $params optional additional parameters
     */
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

    /**
     * checks which validator message is the one to be called
     *
     * @param string $validator validator name
     * @return array|null name and rule for validator method
     */
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

    /**
     * return the array of errors
     *
     * @return array associative array [field][validator] that contains the message
     */
    public function getErrors() {
        return $this->__formdata->errors;
    }

    /**
     * @return bool whether the form is valid
     */
    public function isValid() {
        return !$this->hasErrors();
    }

    /**
     * @return bool whether the form has errors
     */
    public function hasErrors() {
        return is_array($this->__formdata->errors);
    }

    /**
     * validator methods sets an error with the method
     *
     * @param $name name of the form field
     * @param array $params optional list of parameters if message uses sprintf variables
     * @param null $message alternative message to  overwrite the original message
     */
    public function setError($name, $params=array(), $message=null) {
        if (empty($message)) {
            $message = $this->__formdata->fields[$name][$this->__formdata->validator]['message'];
        }
        if (count($params) > 0) {
            $message = call_user_func_array('sprintf', array_merge((array)$message, $params));
        }
        $this->__formdata->errors[$name][$this->__formdata->validator] = $message;
    }

    /**
     * @return object the request from LVC
     */
    protected function request() {
        return $this->__formdata->request;
    }

    /**
     * @return array the params from a validation call
     */
    protected function params() {
        return $this->__formdata->params;
    }

}
