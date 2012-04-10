<?php

class Article extends Model {

    private $id;
    private $date;
    private $title;
    private $teaser;
    private $content;

    private $user = MANY_TO_ONE_RELATION;

    private $location = ONE_TO_ONE_INVERSED_RELATION;

}