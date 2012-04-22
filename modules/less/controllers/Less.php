<?php

class Less extends Controller {

    public static function compile() {
        include('lessc.inc.php');
        $lessCompiler = new lessc(App::get()->request->file);
        header('Content-Type: text/css');
        echo $lessCompiler->parse();
    }

}