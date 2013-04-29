<?php

namespace Scandio\lmvc;

class LVCConfig {

    /**
     * @var object application settings
     */
    private static $config = null;

    /**
     * returns the instance of the singleton object
     * use it like LVCConfig::get() from outside
     *
     * @static
     * @return \Scandio\lmvc\LVCConfig
     */
    public static function get()
    {
        if (is_null(self::$config)) {
            self::$config = new \stdClass();
        }
        return self::$config;
    }

    public static function initialize($config)
    {
        self::$config = (object)$config;
    }
}
