<?php
session_start();
setlocale(LC_ALL, "de_DE");
date_default_timezone_set('Europe/Berlin');

include('../framework/App.php');
App::initialize('config.json');

App::get()->db()->exec("drop table user;");
App::get()->db()->exec("CREATE TABLE user (id integer primary key autoincrement, username text unique not null, password text not null, fullname text);");
$user = new User();
$user->username = 'admin';
$user->fullname = 'Administrator';
$user->password = 'admin';
$user->save();
/**
var_dump($user->id);

var_dump(User::authenticate('admin', 'admin')->id);
var_dump($_SESSION);
var_dump(User::getCurrentUser());
**/


App::get()->db()->exec("drop table article;");
App::get()->db()->exec("create table article (id integer primary key autoincrement, user_id integer not null, date text not null, title text, teaser text, content text);");

$article = new Article();
$article->user = $user;
$article->date = strftime('%Y-%m-%d %H:%M:%S');
$article->title = 'A first try!';
$article->teaser = 'Once you try it you\'ll find a solution - sometimes.';
$article->content = 'This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags.';
//var_dump($article);
$article->save();
//var_dump($article);
//var_dump(Article::findById(1)->user);

App::get()->db()->exec("drop table location;");
App::get()->db()->exec("create table location (id integer primary key autoincrement, article_id integer not null, longitude text, latitude text);");

$location = new Location();
$location->longitude = '4711';
$location->latitude = '0815';
$location->article = $article;
$location->save();
var_dump(App::get()->db()->errorInfo());
var_dump($location->id);
var_dump(Location::findById(1)->article->location);
/**
App::get()->db()->exec("drop table comment;");
App::get()->db()->exec("create table comment (id integer primary key autoincrement, article_id integer not null, date text not null, email text, name text, comment text);");
**/