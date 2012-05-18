<?php

class ModelBuilder implements ArrayBuilder {
    public static function build(Model $renderArgs) {
        return $renderArgs->__data;
    }
}