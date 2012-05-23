<?php

class Application extends Controller {

    public static function index() {
        $articles = Article::findAll('date desc');
        self::setRenderArg('articles', $articles);
        self::render();
    }

}