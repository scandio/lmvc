<?php

class Application extends Controller {

    public static function index() {
        $articles = Article::findAll('date desc');
        App::get()->setRenderArg('articles', $articles);
        self::render();
    }

}