<?php
session_start();
spl_autoload_register('loader');

function loader($className) {
	if (file_exists('framework/'.$className.'.php')) {
		include('framework/'.$className.'.php');
	}
	if (file_exists('controllers/'.$className.'.php')) {
		include('controllers/'.$className.'.php');
	}
    if (file_exists('models/'.$className.'.php')) {
        include('models/'.$className.'.php');
    }
    if (file_exists('plugins/'.$className.'.php')) {
        include('plugins/'.$className.'.php');
    }
}