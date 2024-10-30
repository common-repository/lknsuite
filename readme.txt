=== Social Media Auto Post with lknsuite.com ===
Contributors: alkanulas,lknsuite
Tags: instagram,auto post,auto publish,scheduling,facebook
Requires at least: 3.7
Tested up to: 4.8
Stable tag: 1.0.3
License: GPLv2 or later

Automatically publishes wordpress posts to profiles/pages/groups on Facebook, Twitter, Instagram, Google+, LinkedIn, Tumblr , Other wordpress sites. You do not need to create app for every social media site. lknSuite handles everything for you. You only need lknSuite API key it to work for you


== Description ==

Automatically publishes wordpress posts to

* profiles/pages/groups on Facebook
* Twitter
* Google+(Google Plus)
* Tumblr
* Linkedin
* Instagram
* WordPress.com
* Other Wordpress sites

You do not need to create app for every social media site.

lknSuite handles everything for you.

You only need lknSuite API key it to work for you

The whole process is completely automated.

Just write a new post and either entire post or it’s nicely formatted announcement with backlink will be published to all your configured social networks.

Plugin works with profiles, business pages, community pages, groups, etc. Messages are 100% customizable and adopted for each network requirements.

This plugin requires you to get API key from lknSuite. lknSuite account and API key is completely free. It only takes 2 minutes you to get them

API Key
* Visit [https://www.lknsuite.com/user/login-form/](https://www.lknsuite.com/user/login-form/)
* Login or register on that page. It's free
* After you have logged in follow "Dashboard > Settings > API Credentials" menu (or you can just visit [https://www.lknsuite.com/token/](https://www.lknsuite.com/token/) after you have logged in)

== Installation ==

Upload the lknSuite plugin to your blog, Activate it, then enter your [lknSuite.com API key](https://www.lknsuite.com/token/).

1, 2, 3: You're done!


== Frequently Asked Questions ==

= Is lknSuite publisher Free? =
Yes! lknSuite's core features are and always will be free.

= Should I purchase a paid plan? =
lknSuite's paid services include great services which does not include with free tier like
* collaboration for your social accounts
* more social accounts you can manage
* social media analytics
* competitor analysis
* SEO tools
* priority support.

If you're interested in learning more about the extra layers of protection and advanced tools available, learn more about our [paid plans](https://www.lknsuite.com/pricing/).

= Did not find the answer? =

If you did not find the answer you are looking for, You can use our support page on [https://www.lknsuite.com/support/](https://www.lknsuite.com/support/).

== Changelog ==
= 1.0.3 =
This version does not contain any new feature or interface change. This update contains stability improvements and bug fixes.
* FIXED: Started using current_user_can() and  wp_nonce_field() functions for task/save_settings.php file . WP administrator can use this page
* FIXED: Auto session start attempt is removed from the plugin
* CHANGED: wp_enqueue_script and wp_enqueue_style are being used in addheader() function (in wp_actions.php)


= 1.0.2 =
This version does not contain any new feature or interface change. This update contains stability improvements and bug fixes.
* FIXED: Started using current_user_can() and  wp_nonce_field() functions in meta box actions
* FIXED: Started using wp_remote_get and wp_remote_post functions instead of hardcoded paths CURL requests
* CHANGED: Removed task/accounts.php file. It's now using admin-ajax.php
* CHANGED: Added "defined('_LKNSUITE_PLUGIN') or die('Restricted access');" for the 3th party libraries
* CHANGED: Added more information about plugin parameters

= 1.0.1 =
This version does not contain any new feature or interface change. This update contains stability improvements and bug fixes.
* CHANGED/FIXED: Started using unique function names and constants. For example: define("BASE_PATH",$basePath) changed to define("LKN_BASE_PATH",plugins_url() . '/lknsuite');
* CHANGED/FIXED: Started using Wordpress functions while determining plugin and content directories instead of hardcoded paths. For example define("BASE_PATH",$basePath) become define("LKN_BASE_PATH",plugins_url() . '/lknsuite');
* CHANGED: Plugin description is more understandable (Pervious was the same with "Plugin Name")
* FIXED: Login form link is fixed. https://www.lknsuite.com/login-form/ changed to https://www.lknsuite.com/user/login-form/

= 1.0 =
* Born date is 16th May 2017 :)
