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

App::get()->db()->exec("drop table article;");
App::get()->db()->exec("create table article (id integer primary key autoincrement, user_id integer not null, date text not null, title text, teaser text, content text);");

$article = new Article();
$article->user = $user;
$article->date = strftime('%Y-%m-%d %H:%M:%S');
$article->title = 'A first try!';
$article->teaser = 'Once you try it you\'ll find a solution - sometimes.';
$article->content = 'This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags.';
$article->save();

App::get()->db()->exec("drop table location;");
App::get()->db()->exec("create table location (id integer primary key autoincrement, article_id integer not null, longitude text, latitude text);");

$location = new Location();
$location->longitude = '4711';
$location->latitude = '0815';
$location->article = $article;
$location->save();
//var_dump(App::get()->db()->errorInfo());
//var_dump($location->id);
//var_dump(Location::findById(1)->article->user);

App::get()->db()->exec("drop table comment;");
App::get()->db()->exec("create table comment (id integer primary key autoincrement, article_id integer not null, user_id integer not null, date text not null, content text);");

$comment = new Comment();
$comment->user = $user;
$comment->article = $article;
$comment->date = strftime('%Y-%m-%d %H:%M:%S');
$comment->content = 'This is a comment!';
$comment->save();

$comment2 = new Comment();
$comment2->user = $user;
$comment2->article = $article;
$comment2->date = strftime('%Y-%m-%d %H:%M:%S');
$comment2->content = 'This is a comment! 2';
$comment2->save();

//var_dump(Article::findById(1)->comments);

App::get()->db()->exec("drop table tag;");
App::get()->db()->exec("create table tag (id integer primary key autoincrement, name text);");

$tag = new Tag();
$tag->name = 'tag1';
$tag->save();

$tag2 = new Tag();
$tag2->name = 'tag2';
$tag2->save();

$tag3 = new Tag();
$tag3->name = 'tag3';
$tag3->save();

//$tag->articles->add($article);
//$article->tags->add($tag);

App::get()->db()->exec("drop table article_tag;");
App::get()->db()->exec("create table article_tag (article_id integer, tag_id integer);");
App::get()->db()->exec("insert into article_tag (article_id, tag_id) values (1,1);");
App::get()->db()->exec("insert into article_tag (article_id, tag_id) values (1,3);");

foreach (Article::findById(1)->tags as $tagx) {
    var_dump("\nxxx\n",$tagx->articles);
}
//var_dump($tag->articles);