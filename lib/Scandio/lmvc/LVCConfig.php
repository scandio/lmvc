<?php

namespace Scandio\lmvc;

class LVCConfig {

    /**
     * @var array default configuration to use if a property was not provided
     */
    private static $defaultConfig = array(
        'appPath' => './',
        'controllers' => array('controllers'),
        'controllerPath' => array(),
        'views' => array('./views/'),
        'viewPath' => array(),
        'modules' => array()
    );

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

    public static function initialize($configFile)
    {
        if (!is_null($configFile) && file_exists($configFile)) {
            self::$config = json_decode(file_get_contents($configFile));
            foreach (array_keys(self::$defaultConfig) as $entry) {
                if (!isset(self::$config->$entry)) {
                    self::$config->$entry = self::$defaultConfig[$entry];
                }
            }
        } else {
            self::$config = (object)self::$defaultConfig;
        }
    }
}
