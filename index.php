<?php
session_start();
setlocale(LC_ALL, "de_DE");
date_default_timezone_set('Europe/Berlin');
include_once('framework/App.php');
App::initialize();
App::dispatch();