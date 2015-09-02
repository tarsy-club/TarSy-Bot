<?php
	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Moscow');
	ini_set("display_errors", 1);
	session_start();

	require_once "core/start_class.php";
	$start = new Start();
	$page = $start->startProject();
	if($page) echo $page;
	else{
		header('HTTP/1.0 404 Not Found');
		header('Status: 404 Not Found');
		echo '404 NOT FOUND!';
	}
?>