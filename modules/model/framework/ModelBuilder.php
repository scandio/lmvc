<?php

class ModelBuilder implements ArrayBuilder {
    public static function build($renderArgs) {
        return $renderArgs->__data;
    }
}