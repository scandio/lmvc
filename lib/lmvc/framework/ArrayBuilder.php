<?php

namespace lmvc\framework;

/**
 * Interface for RenderJson ArrayBuilder
 * Return always an array
 */
interface ArrayBuilder {

    /**
     * @static
     * @abstract
     * @param $renderArgs array of mixed data
     * @return array
     */
    public static function build($renderArgs);
}
