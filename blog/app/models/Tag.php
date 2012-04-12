<?php

class Tag extends Model {

    private $id;
    private $name;

    private $articles = MANY_TO_MANY_INVERSED_RELATION;

}