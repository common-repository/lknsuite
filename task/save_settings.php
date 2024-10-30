<?php defined('_LKNSUITE_PLUGIN') or die('Restricted access');

foreach($_REQUEST as $k=>$v){
	$_REQUEST[$k]=trim(ltrim(rtrim($v)));
}

if(!current_user_can("administrator")){
	$_REQUEST['lknsuite_error']='This settings can be changed by Wordpress admins. Please login with your admin account and edit your lknSuite settings';
	require_once LKN_ROOT.LKN_DS.'task'.LKN_DS.'settings.php';
	return;
}
	
	
if (!isset($_POST["lknsuite_admin_save_settins"]) || !wp_verify_nonce($_POST["lknsuite_admin_save_settins"], "save_settings.php")){
	$_REQUEST['lknsuite_error']='Please use the form instead of direct access to the form. We are not able to validate WP nonce field';
	require_once LKN_ROOT.LKN_DS.'task'.LKN_DS.'settings.php';
	return;
}


$lknsuite_api_key=lknInputFilter::filterInput($_REQUEST,"lknsuite_api_key");
if($lknsuite_api_key==''){
	$_REQUEST['lknsuite_error']='Your api key is missing! Plugin needs you to enter your API key to work. It\'s free. You can get it from https://www.lknsuite.com/ ';
	require_once LKN_ROOT.LKN_DS.'task'.LKN_DS.'settings.php';
	return;
}

$lknsuite_api_user_id=lknInputFilter::filterInput($_REQUEST,"lknsuite_api_user_id");
if($lknsuite_api_user_id==''){
	$_REQUEST['lknsuite_error']='Your api user id is missing! Plugin needs you to enter your lknSuite user id to work. It\'s free. You can get it from https://www.lknsuite.com/ ';
	require_once LKN_ROOT.LKN_DS.'task'.LKN_DS.'settings.php';
	return;
}



$post_format=lknInputFilter::filterInput($_REQUEST,"post_format");
if($post_format==''){
	$_REQUEST['lknsuite_error']='Your settings are not saved!Post Format can not be empty';
	require_once LKN_ROOT.LKN_DS.'task'.LKN_DS.'settings.php';
	return;
}

$data=array();
$data['post_format']=$post_format;

$data['lknsuite_api_key']=$lknsuite_api_key;
$data['lknsuite_api_user_id']=$lknsuite_api_user_id;

$sql=array();
$sql['ID']='1';
$sql['settings']=json_encode($data);
$sql['date_created']=time();


$lknsuite=lknSuite::getInstance();
$lknsuite->getSettings();

$db=lknDb::getInstance();
$db->query($db->CreateInsertSql($sql,"#__lknsuite_settings",'REPLACE'));
$db->setQuery();


use phpFastCache\Helper\Psr16Adapter;

$Psr16Adapter = new Psr16Adapter("files");
$Psr16Adapter->clear();


lknSuite_WP_Actions::getInstance()->getAccounts();

lknredirect("admin.php?page=lknsuite_admin.php",'Your settings are saved!')

?>