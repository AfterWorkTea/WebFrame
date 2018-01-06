<?php

include 'include/Website.php';

header("Content-type: text/html;charset=utf-8");

$web = new Website('website.xml');
$web->run();
$web = null;

/*
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

$arg0 = isset($request[0]) ? $request[0] : '';
$arg1 = isset($request[1]) ? $request[1] : '';
*/


?>
