<?php

session_start();
setlocale(LC_ALL, "de_DE");
date_default_timezone_set('Europe/Berlin');

include('../framework/App.php');
App::initialize('config.json');

$task = new Task('this is a second task');
$task->save();