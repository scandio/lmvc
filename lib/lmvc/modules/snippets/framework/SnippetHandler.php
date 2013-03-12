<?php

namespace lmvc\modules\snippets\framework;

abstract class SnippetHandler {

    protected static $snippetFile;
    protected static $prefix='';

    public static function __callStatic($name, $params) {
        $result = "";
        if (method_exists(get_called_class(), $name)) {
            $result = call_user_func_array('static::' . $name, $params);
        } else {
            $app = LVC::get();

            self::$snippetFile = $app->config->appPath .
                'snippets' . DIRECTORY_SEPARATOR . static::$prefix . LVC::camelCaseTo($name) . '.html';
            if (!file_exists(self::$snippetFile)) {
                $reflection = new ReflectionClass(get_called_class());
                $classFileName = $reflection->getFileName();
                $pathElements = explode(DIRECTORY_SEPARATOR, $classFileName);
                if ($pathElements[count($pathElements)-4] == 'modules') {
                    $module = $pathElements[count($pathElements)-3];
                    $modulePath = DIRECTORY_SEPARATOR .
                        implode(DIRECTORY_SEPARATOR, array_slice($pathElements, 1, count($pathElements)-4)) .
                        DIRECTORY_SEPARATOR;
                }
                if ($module) {
                    self::$snippetFile = $modulePath . $module . DIRECTORY_SEPARATOR .
                        'snippets' . DIRECTORY_SEPARATOR . static::$prefix . LVC::camelCaseTo($name) . '.html';
                }
            }
            if (file_exists(self::$snippetFile)) {
                include(self::$snippetFile);
            } else {
                $result = "\n<!-- No snippet file for " . get_called_class() . "::" . $name . "() exists. -->\n";
            }
        }
        return $result;
    }

}