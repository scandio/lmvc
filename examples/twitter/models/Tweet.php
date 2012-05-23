<?php

class Tweet extends Model {

    private $id;
    private $date;
    private $content;

    private $user = MANY_TO_ONE_RELATION;

}