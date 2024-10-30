<?php

/*
 * Plugin Name: Social Media Auto Post with lknsuite.com
 * Plugin URI: http://www.lknsuite.com
 * Description: Automatically publishes wordpress posts to profiles/pages/groups on Facebook, Twitter, Instagram, Google+, LinkedIn, Tumblr , Other wordpress sites. You do not need to create app for every social media site. lknSuite handles everything for you. You only need lknSuite API key it to work for you (API key is free)
 * Author: lknSuite
 * Version: 1.0.3
 * */


if(is_admin()){
	
	if(!defined('_LKNSUITE_PLUGIN')){
		
		define('_LKNSUITE_PLUGIN','1');
		define('LKN_DS',DIRECTORY_SEPARATOR);
		define("LKN_ROOT",__DIR__);
		define("LKN_LIBS",__DIR__.LKN_DS.'lknlibrary');
		
		//something like http://www.sitename.com/sub-directory/another-subdirectory/wp-content/plugins/lknsuite
		define("LKN_BASE_PATH",plugins_url().'/lknsuite');
	}
	
	
	require_once __DIR__.LKN_DS.'wp.php';
	
	$lknsuite=lknSuite::getInstance();
	
	
	$lknsuite->setUserData();
	
	//register admin-ajax account for getting accounts from lknsuite.com
	$lknsuite->registerAccountsAction();
	
	
	$lknsuite->sendTolknSuiteAccount();
	
	//if you are administrator, you can view the menu link
	$lknsuite->addToAdminMenu();
	
	
	$lknsuite->addMetaBox();
	
}

?>