<?php

include 'include/Website.php';

header("Content-type: text/html;charset=utf-8");

$web = new Website('website.xml');
$web->run();
$web = null;

?>
