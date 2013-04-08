<?php

namespace Scandio\lmvc;

abstract class Bootstrap
{
    /**
     * @return void
     */
    public abstract function initialize();

    public static function getNamespace()
    {
        $reflector = new \ReflectionClass(get_called_class());
        return $reflector->getNamespaceName();
    }

    public static function getPath()
    {
        $reflector = new \ReflectionClass(get_called_class());
        return dirname($reflector->getFileName());
    }
}
