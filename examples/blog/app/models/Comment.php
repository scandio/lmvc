<?php

class Comment extends Model {

    private $id;
    private $date;
    private $content;

    private $user = MANY_TO_ONE_RELATION;
    private $article = MANY_TO_ONE_RELATION;

}