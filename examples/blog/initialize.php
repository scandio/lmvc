<?php
session_start();
setlocale(LC_ALL, "de_DE");
date_default_timezone_set('Europe/Berlin');

include('../framework/LVC.php');
LVC::initialize('config.json');

LVC::get()->db()->exec("drop table user;");
LVC::get()->db()->exec("CREATE TABLE user (id integer primary key autoincrement, username text unique not null, password text not null, fullname text);");
LVC::get()->db()->exec("drop table article;");
LVC::get()->db()->exec("create table article (id integer primary key autoincrement, user_id integer not null, date text not null, title text, teaser text, content text);");

LVC::get()->db()->exec("drop table tag;");
LVC::get()->db()->exec("create table tag (id integer primary key autoincrement, name text);");

LVC::get()->db()->exec("drop table article_tag;");
LVC::get()->db()->exec("create table article_tag (article_id integer, tag_id integer);");

LVC::get()->db()->exec("drop table location;");
LVC::get()->db()->exec("create table location (id integer primary key autoincrement, article_id integer not null, longitude text, latitude text);");

LVC::get()->db()->exec("drop table comment;");
LVC::get()->db()->exec("create table comment (id integer primary key autoincrement, article_id integer not null, user_id integer not null, date text not null, content text);");

$user = new User();
$user->username = 'admin';
$user->fullname = 'Administrator';
$user->password = 'admin';
$user->save();

$article = new Article();
$article->user = $user;
$article->date = strftime('%Y-%m-%d %H:%M:%S');
$article->title = 'A first try!';
$article->teaser = 'Once you try it you\'ll find a solution - sometimes.';
$article->content = 'This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags.';
$article->save();

$article2 = new Article();
$article2->user = $user;
$article2->date = strftime('%Y-%m-%d %H:%M:%S');
$article2->title = 'A second try!';
$article2->teaser = 'Twice you try it you\'ll find a solution - sometimes.';
$article2->content = 'This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags. This is a long text with some <b>html</b> tags.';
$article2->save();

$location = new Location();
$location->longitude = '4711';
$location->latitude = '0815';
$location->article = $article;
$location->save();

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
error_log('comment saved'."\n", 3, 'sql.log');

$tag = new Tag();
$tag->name = 'tag1';
$tag->save();
error_log('tag saved'."\n", 3, 'sql.log');

$tag2 = new Tag();
$tag2->name = 'tag2';
$tag2->save();
error_log('tag saved'."\n", 3, 'sql.log');

$tag3 = new Tag();
$tag3->name = 'tag3';
$tag3->save();
error_log('tag saved'."\n", 3, 'sql.log');

$tag4 = new Tag();
$tag4->name = 'tag4';
$tag4->save();
error_log('tag saved'."\n", 3, 'sql.log');


//var_dump($article);
$article->tags->add($tag);
error_log('tag added'."\n", 3, 'sql.log');
$article->tags->add($tag2);
error_log('tag added'."\n", 3, 'sql.log');
$article->tags->add($tag4);
error_log('tag added'."\n", 3, 'sql.log');
echo $article->tags->count();
error_log('tags counted'."\n", 3, 'sql.log');

$article->save();
error_log('article saved'."\n", 3, 'sql.log');

foreach (Article::findById(1)->tags as $tagx) {
    echo "{$tagx->name}\n";
}