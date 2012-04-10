<?php

class Location extends Model {

    private $id;
    private $longitude;
    private $latitude;

    private $article = ONE_TO_ONE_RELATION;

}