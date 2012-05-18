<?php

class Less extends Controller {

    public static function compile() {
        $file = implode('/', App::get()->params);
        if (!file_exists($file)) {
            $file = App::get()->config->modulePath . $file;
        }
        include('lessc.inc.php');
        $lessCompiler = new lessc(App::get()->config->appPath . $file);
        header('Content-Type: text/css');
        echo $lessCompiler->parse();
    }

}