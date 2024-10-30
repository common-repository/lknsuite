<?php defined('_LKNSUITE_PLUGIN') or die('Restricted access'); ?>

<div class="wrap">


    <form method="post" action="admin.php?page=lknsuite_admin.php">

        <input type="hidden" name="task" value="save_settings">
	    <?php  wp_nonce_field("save_settings.php",'lknsuite_admin_save_settins');  ?>

        <h2>Post Formating</h2>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="lknsuite_api_key">lknSuite API KEY</label></th>
                <td><textarea name="lknsuite_api_key" id="lknsuite_api_key" STYLE="width: 100%;" cols="20"
                              rows="1"><?php echo $lknsuite_api_key; ?></textarea>

                    <p>You can your api key and user id with visiting <a href="https://www.lknsuite.com/token/"
                                                                         target="_blank">https://www.lknsuite.com/token/</a>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="lknsuite_api_user_id">lknSuite User ID</label></th>
                <td><textarea name="lknsuite_api_user_id" id="lknsuite_api_user_id" STYLE="width: 100%;" cols="20"
                              rows="1"><?php echo $lknsuite_api_user_id; ?></textarea>

                    <p>You can your api key and user id with visiting <a href="https://www.lknsuite.com/token/"
                                                                         target="_blank">https://www.lknsuite.com/token/</a>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="post_format">Default Post Format</label></th>
                <td><textarea name="post_format" id="post_format" STYLE="width: 100%;" cols="20"
                              rows="1"><?php echo $post_format; ?></textarea>
                    <p>Default value is "New post {POST_TITLE} has been published on {POST_URL}"</p>

                    <p>
	                    <?php add_thickbox(); ?>
                        <a href="<?php echo LKN_BASE_PATH; ?>/task/parameters.php?TB_iframe=true&width=600&height=550"
                           class="thickbox">View Parameters Information</a>
                    </p>


                </td>
            </tr>


            </tbody>
        </table>


        <p class="submit"><input name="submit" class="button button-primary" value="Save"
                                 type="submit"></p></form>

</div>


<div class="wrap">


    <h1>lknSuite</h1>
    <p>lknSuite is suite of online productivity tools. One of its tools is social media management. You can schedule &
        publish posts to the supported social networks. You can also engage with your social media followers with one
        single dashboard. Supported social media sites are below<br/>
    <ol>
        <li>Twitter</li>
        <li>Facebook</li>
        <li>Google+</li>
        <li>Linkedin</li>
        <li>Tumblr</li>
        <li>Instagram (No mobile app is required. lknSuite can directly post to Instagram)</li>
        <li>Wordpress.com blogs</li>
        <li>Other Wordpress sites</li>
    </ol>


    <br/>
    You can get more information with visiting <a href="https://www.lknsuite.com/" target="_blank">https://www.lknsuite.com/</a>


    <br/>
    You can get more information about the premium offfers on <a href="https://www.lknsuite.com/pricing/"
                                                                 target="_blank">https://www.lknsuite.com/pricing/</a>


    </p>


    <h1>User ID & API Key</h1>
    <p>User ID and API key is free. You can get them quickly with the following steps<br/>
    <ol>
        <li>Visit <a href="https://www.lknsuite.com/user/login-form/"
                     target="_blank">https://www.lknsuite.com/user/login-form/</a></li>
        <li>Login or register on that page. It's free</li>
        <li>After you have logged in follow "Dashboard > Settings > API Credentials" menu (or you can just visit <a
                    href="https://www.lknsuite.com/token/" target="_blank">https://www.lknsuite.com/token/</a> after you
            have logged in)
        </li>
    </ol>

    </p>


</div>