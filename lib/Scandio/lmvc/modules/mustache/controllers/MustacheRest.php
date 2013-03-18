<?php

namespace Scandio\lmvc\modules\mustache\controllers;

use \Scandio\lmvc\framework\Controller;

class MustacheRest extends Controller {

    public static function render($template) {
        echo Mustache::render($template, self::request());
    }

}