<?php

defined('_LKNSUITE_PLUGIN') or die('Restricted access');

use phpFastCache\Helper\Psr16Adapter;


class lknSuite_WP_Actions{
	
	
	function __construct(){
	}
	
	
	/**
	 *
	 * @return lknSuite_WP_Actions
	 */
	public static function getInstance(){
		static $_instance;
		if(!isset($_instance)){
			$_instance=new lknSuite_WP_Actions();
		}
		
		return $_instance;
		
	}
	
	
	function setUserData(){
		global $current_user;
  
		if(isset($current_user->ID)){
			
			$user=lknUser::getInstance();
			$user->set('id',$current_user->ID);
			$user->set('email',$current_user->user_email);
			$user->set('name',$current_user->user_nicename);
			$user->set('username',$current_user->user_login);
			$user->set('registerDate',$current_user->user_registered);
			$user->set('lastvisitDate',$current_user->last_activity);
			$user->set('my',$current_user);
			$user->set('usertype',$current_user->roles);
   
		}
	}
	
	
	
	/**
	 * Register a custom menu page.
	 */
	function addToAdminMenu(){
		
		
		add_menu_page('Social Networks Auto Poster','Social Networks Auto Poster With lknSuite.com','manage_options',"lknsuite_admin.php",array(
			$this,
			'adminPage'
		));
	}
	
	
	function adminPage(){
		
		
		require_once LKN_ROOT.LKN_DS.'lknsuite_admin.php';
		
		
		return;
		
	}
	
	
	function addMetaBox(){
		add_meta_box("demo-meta-box","Auto Post To Social Media With lknSuite.com",array(
			$this,
			'custom_meta_box_markup'
		),null,"side","high");
		
		add_action('admin_enqueue_scripts',array($this,'addheader'));
	}
	
	
	function addheader(){
  
		wp_enqueue_script('lknsuite_main',LKN_BASE_PATH.'/views/assets/js/main.js');
		wp_enqueue_style('lknsuite_css',LKN_BASE_PATH."/views/assets/css/main.css");
	}
	
	function post_updated($post_id,$post,$update){
		
		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return $post_id;
		}
		
		if(!isset($_POST["lknsuite_custom_meta_box_markup"]) || !wp_verify_nonce($_POST["lknsuite_custom_meta_box_markup"],basename(__FILE__))){
			return $post_id;
		}
		
		
		if(!current_user_can("edit_post",$post_id)){
			return $post_id;
		}
		
		
		$lknsuite=lknSuite::getInstance();
		
		
		$lknsuite_accounts   =lknInputFilter::filterInput($_REQUEST,"lknsuite_accounts");
		$lknsuite_post_format=lknInputFilter::filterInput($_REQUEST,"lknsuite_post_format");
		if($lknsuite_post_format==''){
			$Psr16Adapter=new Psr16Adapter("files");
			$keyword     ='schedule_result';
			if(!$Psr16Adapter->has($keyword)){
				$msg="Post format can not be empty. Social media posting is not done. Please write your own post format or leave it default";
				
				$Psr16Adapter->set($keyword,$msg,3600);
			}
		}
		
		if(count($lknsuite_accounts)>0){
			
			$post_title=lknInputFilter::filterInput($_REQUEST,"post_title");
			
			$post_content=preg_replace('#<br[/\s]*>#si',"\n",$post->post_content);
			$post_content=trim($post_content);
			$post_content=strip_tags($post_content);
			
			$post_featured_url=get_the_post_thumbnail_url($post_id,'full');
			
			if($post_featured_url){
				
				/**
				 * $post_featured_url gives the full url like http://www.site.com/subdirectory/wp-content/uploads/2017/05/Screen-Shot-2017-05-02-at-09.39.28.png
				 * we need the directory /home/public_html/user_name/subdirectory/wp-content/uploads/2017/05/Screen-Shot-2017-05-02-at-09.39.28.png
				 * because we need to
				 * 1) make sure that image is smaller than 3mb
				 * 2) convert it to base64 before adding it to lknsuite.com queue
				 */
				$post_featured_url=ABSPATH.str_replace(array(get_site_url(),'/'),array(
						'',
						LKN_DS
					),$post_featured_url);
				
				
				$size=filesize($post_featured_url)/1048576;//mb
				
				
				if($size>3){
					$sql                =array();
					$sql['ID']          ='2';
					$sql['settings']    ="lknSuite.com accepts maximum 3mb images. Social media posting is not done. Please try again with smaller size image ";
					$sql['date_created']=time();
					
					$db=lknDb::getInstance();
					$db->query($db->CreateInsertSql($sql,"#__lknsuite_settings",'REPLACE'));
					$db->setQuery();
					
					
					return;
				}
				
				$type=pathinfo($post_featured_url,PATHINFO_EXTENSION);
				$data=file_get_contents($post_featured_url);
				
				$post_featured_url='data:image/'.$type.';base64,'.base64_encode($data);
				
				
			}else{
				$post_featured_url='';
			}
			
			
			$post_url =get_permalink($post_id);
			$post_tags=wp_get_post_tags($post_id,array('fields'=>'names'));
			if(count($post_tags)>0){
				$post_tags=trim(implode(',',$post_tags));
			}else{
				$post_tags='';
			}
			
			$post_categories=wp_get_post_categories($post_id);
			$cats           =array();
			
			foreach($post_categories as $c){
				$cat   =get_category($c);
				$cats[]=$cat->name;
			}
			
			$text=new lknSuite_WP_Actions_Text();
			
			
			$lknsuite->loadParams();
			$lknsuite_api_key    =$lknsuite->getPostParam("lknsuite_api_key");
			$lknsuite_api_user_id=$lknsuite->getPostParam("lknsuite_api_user_id");
			
			
			$lknsuite_post_format=str_replace('{POST_TITLE}',$post_title,$lknsuite_post_format);
			$lknsuite_post_format=str_replace('{SITE_NAME}','',$lknsuite_post_format);
			$lknsuite_post_format=str_replace('{POST_CONTENT}',$post_content,$lknsuite_post_format);
			$lknsuite_post_format=str_replace('{POST_URL}',$post_url,$lknsuite_post_format);
			$lknsuite_post_format=str_replace('{TAGS}',$post_tags,$lknsuite_post_format);
			
			
			$lknsuite_post_format=str_replace('{CATS}',implode(',',$cats),$lknsuite_post_format);
			
			
			if(strpos($lknsuite_post_format,'{POST_CONTENT_')!==false){
				preg_match("%({POST_CONTENT_(.*?)})%is",$lknsuite_post_format,$letter_count);
				if(isset($letter_count[2])){
					$letter_count        =$letter_count[2];
					$lknsuite_post_format=str_replace('{POST_CONTENT_'.$letter_count.'}',$text->limitText($post_content,$letter_count),$lknsuite_post_format);
				}
			}
			
			
			if(strpos($lknsuite_post_format,'{WORDS_')!==false){
				preg_match("%({WORDS_(.*?)})%is",$lknsuite_post_format,$letter_count);
				if(isset($letter_count[2])){
					$letter_count        =$letter_count[2];
					$lknsuite_post_format=str_replace('{WORDS_'.$letter_count.'}',$text->limitWords($post_content,$letter_count),$lknsuite_post_format);
				}
			}
			
			
			$seleted_accounts_types=array();
			$accounts              =json_decode($this->getAccounts());
			
			foreach($accounts->data as $account){
				if(in_array($account->account_id,$lknsuite_accounts)){
					$seleted_accounts_types[]=$account->sm_site;
				}
			}
			
			
			// set post fields
			$post=[
				'token'                 =>$lknsuite_api_key,
				'user_id'               =>$lknsuite_api_user_id,
				'message'               =>$lknsuite_post_format,
				'seleted_accounts'      =>implode(',',$lknsuite_accounts),
				'seleted_accounts_types'=>implode(',',$seleted_accounts_types),
				'posting_date'          =>300,//lknsuite will schedule it to 300 seconds later
				'email_me'              =>0,
				'coords'                =>'',
				'locations'             =>'',
				'fb_privacy'            =>'',
				'linkedin_privacy'      =>'',
				'google_privacy'        =>'',
				'in_reply_to_status_id' =>'',
				'tumblr_post_title'     =>$post_title,
				'tumblr_post_tags'      =>$post_tags,
				'tumblr_use_bbcode'     =>'0',
				'wp_post_title'         =>$post_title,
				'wp_allow_comments'     =>'1',
				'wp_post_tags'          =>$post_tags,
				'wp_use_bbcode'         =>'',
				'wp_cats'               =>'',
				'postid'                =>'',
				'as_draft'              =>'0',
				'instagram_post_type'   =>'photo',
				'reject_reason'         =>'',
				'scheduleit'            =>'0',
				'sendnow'               =>'',
				'og_image'              =>'',
				'og_title'              =>$post_title,
				'og_description'        =>$post_content,
				'og_url'                =>$post_url,
				'toexistingalbums'      =>'',
				'fb_new_album'          =>'',
				'post_images'           =>$post_featured_url
			];
			
			
			$args=array(
				'body'       =>$post,
				'timeout'    =>'5',
				'redirection'=>'5',
				'httpversion'=>'1.0',
				'blocking'   =>true,
				'headers'    =>array(),
				'cookies'    =>array()
			);
			
			
			$response=wp_remote_post('https://www.lknsuite.com/api/publisher/save_post',$args);
			
			$response=json_decode($response['body']);
			
			$Psr16Adapter=new Psr16Adapter("files");
			$keyword     ='schedule_result';
			if(!$Psr16Adapter->has($keyword)){
				if($response->status=='1'){
					$msg="Your message is successfully sent to lknsuite.com and it will be sent to the social media site in 5 minutes ";
				}else{
					$msg=lknStripSlash($response->msg);
				}
				$Psr16Adapter->set($keyword,$msg,3600);
			}
		}
	}
	
	function get($var){
		if(isset($this->$var)){
			return $this->$var;
		}else{
			return null;
		}
	}
	
	
	function custom_meta_box_markup($meta_boxes){
		
		$lknsuite    =lknSuite::getInstance();
		$msg         ='';
		$Psr16Adapter=new Psr16Adapter("files");
		$keyword     ='schedule_result';
		if($Psr16Adapter->has($keyword)){
            $msg=$Psr16Adapter->get($keyword);
		}
		
		
		if($msg!=''){
			$Psr16Adapter->delete($keyword);
			
			?>

            <p align="center" id="lknsuite_infomessage">
                <div class="updated notice is-dismissible">
            <p><strong><?php echo lknStripSlash($msg); ?></strong></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Hide</span></button>
            </div>
            </p>
			
			<?php
		}
		
		
		$lknsuite->loadParams();
		$lknsuite_api_key    =trim(ltrim(rtrim($lknsuite->getPostParam("lknsuite_api_key"))));
		$lknsuite_api_user_id=trim(ltrim(rtrim($lknsuite->getPostParam("lknsuite_api_user_id"))));
		$post_format         =$lknsuite->getPostParam('post_format');
		
		
		?>
        <div class="lknsuite_categorydiv">
			
			<?php wp_nonce_field(basename(__FILE__),"lknsuite_custom_meta_box_markup"); ?>
			
			<?php if($lknsuite_api_key!='' && $lknsuite_api_user_id!='' && $post_format!=''){ ?>
                <div class="_lknsuite_meta_box">
                    Loading...
                </div>
                
                <table width="100%">
                    <tbody>

                    <tr>
                        <td scope="row"><label for="lknsuite_post_format">Post Format</label></td>

                    </tr>
                    <tr>
                        <td><textarea name="lknsuite_post_format" id="lknsuite_post_format" style="width: 100%;"
                                      cols="20"
                                      rows="3"><?php echo $post_format; ?></textarea>

                            <div class="lknsuite_how">
								<?php add_thickbox(); ?>
                                <a href="<?php echo LKN_BASE_PATH; ?>/task/parameters.php?TB_iframe=true&width=600&height=550"
                                   class="thickbox">View Parameters Information</a><br/><br/>

                                <a href="<?php echo LKN_BASE_PATH; ?>/task/how.php?TB_iframe=true&width=600&height=550"
                                   class="thickbox">How this plugin works</a>
                            </div>

                    </tr>


                    </tbody>
                </table>
			<?php }else{
				?>

                <div>
                    <h5>Please enter your API key and User ID before you start using it. You can edit your settings from
                        <a href="<?php echo admin_url(); ?>admin.php?page=lknsuite_admin.php">this link</a></h5>

                </div>
				<?php
			} ?>


        </div>
		<?php
		
		
		add_action('admin_footer',array($this,'lknsuite_accounts_javascript'));
		
	}
	
	function getAccounts(){
		$lknsuite=lknSuite::getInstance();
		
		$lknsuite->loadParams();
		$lknsuite_api_key    =trim(ltrim(rtrim($lknsuite->getPostParam("lknsuite_api_key"))));
		$lknsuite_api_user_id=trim(ltrim(rtrim($lknsuite->getPostParam("lknsuite_api_user_id"))));
		
		
		$keyword     ='accounts_';
		$Psr16Adapter=new Psr16Adapter("files");
		
		if(!$Psr16Adapter->has($keyword)){
			
			
			// Setter action
			$url="https://www.lknsuite.com/api/publisher/accounts?user_id=$lknsuite_api_user_id&token=$lknsuite_api_key";
			
			
			// First, we try to use wp_remote_get
			$response=wp_remote_get($url);
			if(!is_wp_error($response)){
				
				$response=$response['body'];
				if($response!=''){
					$row=json_decode($response);
					
					if(isset($row->status) && $row->status=='1'){
						$Psr16Adapter->set($keyword,$response,86400);
					}
				}else{
					//no response or empty body from server
					lknredirect("admin.php?page=lknsuite_admin.php",'','Your settings are saved but we are able to get response from lksuite.com. Please wait 2 minutes are try it again');
				}
				
			}else{
				
				//we are not able to get content because of CURL - hosting issue wp_remote_get returns WP_Error
				$wp_error_text=$response->get_error_message();
				lknredirect("admin.php?page=lknsuite_admin.php",'','Your settings are saved but we are able to get your because of your hosting. CURL returns '.urlencode($wp_error_text));
			}
			
		}else{
			// Getter action
			$response=$Psr16Adapter->get($keyword);
		}
		
		
		
		return $response;
	}
	
	
	function lknsuite_accounts(){
		
		
		echo $this->getAccounts();
		
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	function lknsuite_accounts_javascript(){ ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {


                var data = {
                    'action': 'lknsuite_accounts',
                    'dataType': 'json',
                    "contentType": "application/json; charset=utf-8"

                };


                jQuery.ajax({
                    type: 'POST',
                    dataType: "json",
                    data: data,
                    url: ajaxurl,
                    beforeSend: function () {

                    },
                    success: function (returnData) {
                        lknsuite.getaccounts(returnData);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                    },
                    complete: function () {

                    }
                });
            });
        </script> <?php
	}
	
}

class lknSuite_WP_Actions_Text{
	function __construct(){
	}
	
	
	/**
	 * bir metin içerisinde belirli sayıda karakteri alır
	 *
	 * @param string  $string
	 * @param integer $length
	 * @param string  $replacer
	 *
	 * @return string
	 */
	function limitText($string,$length,$replacer='...'){
		
		$l=strlen($string);
		if($string!='' && $l>0 && $l>$length){
			if(function_exists('mb_substr')){
				return mb_substr($string,0,$length,'utf-8').$replacer;
			}else{
				return substr($string,0,$length).$replacer;
			}
			
		}else{
			return $string;
		}
	}
	
	
	/**
	 * @param $string
	 * @param $word_limit
	 *
	 * @return string
	 */
	function limitWords($string,$word_limit,$replacer='...'){
		$words=explode(' ',$string);
		if(count($words)>$word_limit){
			return implode(' ',array_slice($words,0,$word_limit)).$replacer;
		}else{
			return $string;
		}
		
		
	}
}

?>