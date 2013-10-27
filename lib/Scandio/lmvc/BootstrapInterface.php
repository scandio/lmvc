<?php

namespace Scandio\lmvc;

/**
 * Interface BootstrapInterface
 * @package Scandio\lmvc
 */
interface BootstrapInterface
{
    /**
     * @return void
     */
    public function initialize();

    /**
     * @return string
     */
    public static function getNamespace();

    /**
     * @return string
     */
    public static function getPath();

} 