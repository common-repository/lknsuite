<?php defined('_LKNSUITE_PLUGIN') or die('Restricted access'); ?>
<?php

$task=lknInputFilter::filterInput($_REQUEST,"task",'settings');

if(file_exists(LKN_ROOT.LKN_DS.'task'.LKN_DS.$task.'.php')){
	
	require_once LKN_ROOT.LKN_DS.'task'.LKN_DS.$task.'.php';
}else{
	echo "<h1>Task is not found</h1>";
}
?>
