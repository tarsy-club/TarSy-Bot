<?php
/**
 * TarSy - A PHP Framework For Web Artisans
 *
 * @package  TarSy
 * @author   Ruslan Rozhkov <ruslan399@gmail.com>
 */

error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');
ini_set("display_errors", 1);
session_start();

//define("start_time", microtime(true));

require_once __DIR__."/core/start_class.php";
$start = new Start();
$page = $start->startProject();
if($page) echo $page;
else{
	header('HTTP/1.0 404 Not Found');
	header('Status: 404 Not Found');
	echo '404 Not Found';
}

//printf("<div>%.5f cек</div>",microtime(true)-start_time);

?>