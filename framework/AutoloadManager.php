<?php

class AutoloadManager {

    private static $basePath = '';
    private static $modulePath = 'modules/';
    private static $paths = array('framework', 'controllers', 'models');

    public static function initialize() {
        $modules = array_filter(scandir(self::$basePath.self::$modulePath), function($dirFile) {
            return (!is_file($dirFile) && $dirFile != '.' && $dirFile != '..');
        });
        set_include_path(get_include_path() . self::getPath($modules));
        spl_autoload_register(function ($classname){
            spl_autoload($classname);
        });
    }

    private function getPath($modules=array()) {
        $result = PATH_SEPARATOR . self::$basePath . implode(PATH_SEPARATOR . self::$basePath, self::$paths);
        foreach ($modules as $module) {
            $result .= PATH_SEPARATOR . self::$basePath . self::$modulePath . $module . '/' .
                implode(PATH_SEPARATOR . self::$basePath . self::$modulePath . $module . '/' , self::$paths);
        }
        return $result;
    }

}