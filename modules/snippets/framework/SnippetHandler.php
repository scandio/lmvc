<?php

abstract class SnippetHandler {

    protected static $snippetFile;

    public static function __callStatic($name, $params) {
        self::$snippetFile = LVC::get()->config->appPath . 'snippets/' . LVC::camelCaseTo($name) . '.html';
        self::$snippetFile = (file_exists(self::$snippetFile)) ? self::$snippetFile : LVC::get()->config->modulePath . 'snippets/snippets/' . LVC::camelCaseTo($name) . '.html';
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