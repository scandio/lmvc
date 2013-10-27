<?php

namespace Scandio\lmvc;

/**
 * Class Bootstrap
 * @package Scandio\lmvc
 */
abstract class Bootstrap implements BootstrapInterface
{
    /**
     * @return void
     */
    public abstract function initialize();

    /**
     * @return string
     */
    public static function getNamespace()
    {
        $reflector = new \ReflectionClass(get_called_class());
        return $reflector->getNamespaceName();
    }

    /**
     * @return string
     */
    public static function getPath()
    {
        $reflector = new \ReflectionClass(get_called_class());
        return dirname($reflector->getFileName());
    }
}
