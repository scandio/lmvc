<?php

namespace Scandio\lmvc\modules\snippets\framework;

use \Scandio\lmvc\framework\LVC;

abstract class SnippetHandler {

    protected static $snippetFile;
    protected static $prefix='';
    protected static $snippetPath = array();

    public static function __callStatic($name, $params) {
        $result = "";
        if (method_exists(get_called_class(), $name)) {
            $result = call_user_func_array('static::' . $name, $params);
        } else {
            self::$snippetFile = self::searchSnippet(static::$prefix . LVC::camelCaseTo($name) . '.html');
            if (self::$snippetFile) {
                include(self::$snippetFile);
            } else {
                $result = "\n<!-- No snippet file for " . get_called_class() . "::" . $name . "() exists. -->\n";
            }
        }
        return $result;
    }

    /**
     * registers a new snippet directory to search for the snippets
     *
     * @param array|string $path specifies the directory to register
     */
    public static function registerSnippetDirectory($path) {
        if (is_array($path)) {
            $snippetPath = implode(DIRECTORY_SEPARATOR, $path);
        } elseif (is_string($path)) {
            $snippetPath = $path;
        } else {
            // TODO: Error - couldn't register SnippetDirectory
            return;
        }

        $class = get_called_class();
        if (isset(self::$snippetPath[$class])) {
            array_unshift(self::$snippetPath[$class], $snippetPath);
        } else {
            self::$snippetPath[$class] = array($snippetPath);
        }
    }

    /**
     * searches for the snippet in the registered directories
     *
     * @return string|bool either the snippet's full path or false
     */
    private function searchSnippet($snippet) {
        $class = get_called_class();
        if (!isset(self::$snippetPath[$class])) {
            return false;
        }
        foreach (self::$snippetPath[$class] as $path) {
            $snippetPath = LVC::get()->config->appPath . $path . DIRECTORY_SEPARATOR . $snippet;
            if (file_exists($snippetPath)) {
                return $snippetPath;
            }
        }
        return false;
    }

}