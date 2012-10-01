<?php

abstract class SnippetHandler {

    protected static $snippetFile;
    protected static $prefix='';

    public static function __callStatic($name, $params) {
        self::$snippetFile = LVC::get()->config->appPath . 'snippets/' . static::$prefix . LVC::camelCaseTo($name) . '.html';
        self::$snippetFile = (file_exists(self::$snippetFile)) ? self::$snippetFile : LVC::get()->config->modulePath . 'snippets/snippets/' . static::$prefix . LVC::camelCaseTo($name) . '.html';
        error_log(self::$snippetFile);
        if (file_exists(self::$snippetFile)) {
            ob_start();
            if (method_exists(get_called_class(), $name)) {
                call_user_func_array('static::' . $name, $params);
            } else {
                include(self::$snippetFile);
            }
            $result = ob_get_contents();
            ob_end_clean();
        } else {
            $result = "\n<!-- No snippet file for " . get_called_class() . "::" . $name . "() exists. -->\n";
        }
        return $result;
    }

}