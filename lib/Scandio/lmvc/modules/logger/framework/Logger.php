<?php

namespace Scandio\lmvc\modules\logger\framework;

use \Scandio\lmvc\LVC;

/**
 * Logger writes in the Apache error log file
 * if no mode is given PROD mode is assumed
 * you can set the mode as a variable in config.json
 * like "mode":"DEV" you can call it like
 * Logger::write('my message');
 * this will only be logged in DEV mode
 * Logger::write('my prod message', Logger::PROD);
 * this will be logged in DEV and PROD mode
 */
class Logger {

    const DEV = 'dev';
    const PROD = 'prod';

    public static function write($entry, $mode = null) {
        $mode = (is_null($mode)) ? self::DEV : $mode;
        if (strtolower(LVC::get()->config->mode) == self::DEV || $mode == self::PROD) {
            error_log('LVC::Logger (' . $mode . ') -- ' . $entry);
        }
    }

}