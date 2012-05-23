<?php

class Less extends Controller {

    public static function compile() {
        $cacheFile = implode('-', LVC::get()->params);
        $file = implode('/', LVC::get()->params);
        if (!file_exists($file)) {
            $file = LVC::get()->config->modulePath . $file;
        }
        if (file_exists($cacheFile) && filemtime($file) <= filemtime($cacheFile)) {
            $cssStream = "/* cached result from less compiler */\n" . file_get_contents($cacheFile);
        } else {
            include('lessc.inc.php');
            $lessCompiler = new lessc(LVC::get()->config->appPath . $file);
            $cssStream = $lessCompiler->parse();
            file_put_contents($cacheFile, $cssStream);
            $cssStream = "/* compiled result from less compiler */\n" . $cssStream;
        }
        header('Content-Type: text/css');
        echo $cssStream;
    }

}