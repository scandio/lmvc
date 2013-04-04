<?php

namespace Scandio\lmvc;

/**
 * Interface for RenderJson ArrayBuilder
 * Return always an array
 */
interface ArrayBuilderInterface
{

    /**
     * @static
     * @abstract
     * @param array $renderArgs array of mixed data
     * @return array
     */
    public static function build($renderArgs);
}
