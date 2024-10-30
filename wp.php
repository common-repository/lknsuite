<?php

defined('_LKNSUITE_PLUGIN') or die('Restricted access');


require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'cache'.LKN_DS.'CacheException.php';
require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'cache'.LKN_DS.'CacheItemInterface.php';
require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'cache'.LKN_DS.'CacheItemPoolInterface.php';
require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'cache'.LKN_DS.'InvalidArgumentException.php';


require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'simple-cache'.LKN_DS.'CacheException.php';
require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'simple-cache'.LKN_DS.'CacheInterface.php';
require_once LKN_LIBS.LKN_DS.'psr'.LKN_DS.'simple-cache'.LKN_DS.'InvalidArgumentException.php';


require_once LKN_LIBS.LKN_DS.'cache'.LKN_DS.'src'.LKN_DS.'autoload.php';


use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;

use phpFastCache\Helper\Psr16Adapter;


class lknSuite{
	
	private $_db;
	private $_db_prefix_mask;
	
	private $_wp_actions;
	
	private $_post_params;
	
	
	public $_vars;
	
	
	function __construct(){
		$this->import();
		
		global $wpdb;
		
		
		$this->_db            = &$wpdb;
		$this->_db_prefix_mask="#__";
		
		require_once LKN_ROOT.LKN_DS.'wp_actions.php';
		
		$this->_wp_actions=lknSuite_WP_Actions::getInstance();
		
		$this->_vars=array();
  
	}
	
	
	function get($var){
		if(isset($this->$var)){
			return $this->$var;
		}else{
			return null;
		}
	}
	
	
	/**
	 *
	 * @return lknSuite
	 */
	public static function getInstance(){
		static $_instance;
		if(!isset($_instance)){
			$_instance=new lknSuite();
		}
		
		return $_instance;
		
	}
	
	
	private function import(){
		
		require_once LKN_LIBS.LKN_DS.'phpinputfilter'.LKN_DS.'phpinputfilter.inputfilter.php';
		
		require_once LKN_LIBS.LKN_DS.'lknlibrary'.LKN_DS.'registery.php';
		require_once LKN_LIBS.LKN_DS.'lknlibrary'.LKN_DS.'class.template.php';
		require_once LKN_LIBS.LKN_DS.'lknlibrary'.LKN_DS.'class.user.php';
		require_once LKN_LIBS.LKN_DS.'lknlibrary'.LKN_DS.'functions.php';
		require_once LKN_LIBS.LKN_DS.'lknlibrary'.LKN_DS.'class.db.php';
	}
	
	function setUserData(){
		add_action('init',array($this->_wp_actions,'setUserData'));
	}
	
	function addToAdminMenu(){
		add_action('admin_menu',array($this->_wp_actions,'addToAdminMenu'));
	}
	
	
	function getAdminPage(){
		
		ob_clean();
		
		ob_start(); // Start output buffering
		$this->error();
		require_once LKN_ROOT.LKN_DS.'lknsuite_admin.php';
		
		$contents=ob_get_contents(); // Get the contents of the buffer
		ob_end_clean(); // End buffering and discard
		
		
		return $contents;
	}
	
	
	function loadParams(){
		
		
		$row=$this->getSettings();
		
		if(isset($row->settings) && $row->settings!=''){
			
			/**
			 *
			 * $tmpl->set('lknsuite_api_key',$lknsuite_api_key);
			 * $tmpl->set('lknsuite_api_user_id',$lknsuite_api_user_id);
			 *
			 *
			 * $tmpl->set('post_format',$post_format);
			 */
			
			$this->_post_params=json_decode($row->settings);
			
		}else{
			$this->_post_params='';
		}
		
	}
	
	private function createTable(){
		$db=lknDb::getInstance();
		$db->query("CREATE TABLE IF NOT EXISTS `#__lknsuite_settings` (
  `ID` tinyint(1) NOT NULL,
  `settings` mediumtext NOT NULL,
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		$db->setQuery();
		
		
		$db->query("ALTER TABLE `#__lknsuite_settings` ADD UNIQUE KEY `ID` (`ID`)");
		$db->setQuery();
		
		
		$sql                =array();
		$sql['ID']          ='1';
		$sql['settings']    ='';
		$sql['date_created']=time();
		
		$db->query($db->CreateInsertSql($sql,"#__lknsuite_settings",'REPLACE'));
		$db->setQuery();
		
		return $this->getSettings();
	}
	
	
	function getSettings(){
		
		$Psr16Adapter=new Psr16Adapter("files");
		
		$keyword='settings';
		
		if(!$Psr16Adapter->has($keyword)){
			$db=lknDb::getInstance();
			$db->query("SELECT * FROM #__lknsuite_settings WHERE ID='1'");
			$db->setQuery();
			if($db->getErrorMessage()!=''){
				return $this->createTable();
			}else{
				$row=$db->loadObject();
				$Psr16Adapter->set($keyword,$row,3600);
			}
			
		}else{
			// Getter action
			$row=$Psr16Adapter->get($keyword);
		}
		
		return $row;
	}
	
	
	function registerAccountsAction(){
		add_action('wp_ajax_lknsuite_accounts',array($this->_wp_actions,'lknsuite_accounts'));
	}
	
	function getPostParam($param){
		if(isset($this->_post_params->$param)){
			return $this->_post_params->$param;
		}else{
			return '';
		}
	}
	
	function addMetaBox(){
		add_action("add_meta_boxes",array($this->_wp_actions,'addMetaBox'));
	}
	
	
	function sendTolknSuiteAccount(){
		//		add_action('save_post',array($this->_wp_actions,'post_updated'),10,3);
		add_action('post_updated',array($this->_wp_actions,'post_updated'),10,3);
	}
	
	
	function error(){
		
		
		$error=lknStripSlash(lknInputFilter::filterInput($_REQUEST,'lknsuite_error'));
		$msg  =lknStripSlash(lknInputFilter::filterInput($_REQUEST,'lknsuite_msg'));
		
		
		if($error!='' || $msg!=''){
			?>
			<?php if($error!=''){ ?>
                <p align="center" id="lknsuite_errormessage">

                    <div class="error settings-error notice is-dismissible" id="">
                <p><strong><?php echo $error; ?></strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Hide</span>
                </button>
                </div>

                </p>
				<?php
			}elseif($msg!=''){
				
				?>
                <p align="center" id="lknsuite_infomessage">
                    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php echo $msg; ?></strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Hide</span>
                </button>

                </div>

                </p>
				
				<?php
			}
			?>
			
			<?php
		}
	}
}

?>