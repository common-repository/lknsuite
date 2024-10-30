<?php defined('_LKNSUITE_PLUGIN') or die('Restricted access');



$tmpl=lknTemplate::getInstance();


$lknsuite=lknSuite::getInstance();
$row=$lknsuite->getSettings();

$default='New post {POST_TITLE} has been published on {POST_URL}';

if(isset($row->settings) && $row->settings!=''){
	$row=json_decode($row->settings);
}

$lknsuite_api_key=lknInputFilter::filterInput($_REQUEST,"lknsuite_api_key");
if($lknsuite_api_key=='' && !isset($_POST['lknsuite_api_key'])){
	$lknsuite_api_key=(isset($row->lknsuite_api_key)?$row->lknsuite_api_key:'');
}


$lknsuite_api_user_id=lknInputFilter::filterInput($_REQUEST,"lknsuite_api_user_id");
if($lknsuite_api_user_id=='' && !isset($_POST['lknsuite_api_user_id'])){
	$lknsuite_api_user_id=(isset($row->lknsuite_api_user_id)?$row->lknsuite_api_user_id:'');
}

$post_format=lknInputFilter::filterInput($_REQUEST,"post_format");
if($post_format=='' && !isset($_POST['post_format'])){
	$post_format=(isset($row->post_format)?$row->post_format:$default);
}

$tmpl->set('lknsuite_api_key',$lknsuite_api_key);
$tmpl->set('lknsuite_api_user_id',$lknsuite_api_user_id);

$tmpl->set('post_format',$post_format);


echo $tmpl->fetch_view("settings");
?>
