<?php


define('_LKNSUITE_PLUGIN','1');
define('LKN_DS',DIRECTORY_SEPARATOR);
define("LKN_ROOT",dirname(__DIR__));
define("LKN_LIBS",LKN_ROOT.LKN_DS.'lknlibrary');

require_once LKN_ROOT.LKN_DS.'wp.php';
$lknsuite=lknSuite::getInstance();

$tmpl=lknTemplate::getInstance();



echo $tmpl->fetch_view("parameters");
?>
