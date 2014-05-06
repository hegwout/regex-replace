<?php
/*
Plugin Name: Regex replace
Plugin URI: http://heoffice.com/
Description: Find and replace with Regex
Version: 0.0.1
Author: hegw
Author URI: http://weibo.com/u/2283075387/home?wvr=5
*/
/**
 * Thanks:
 *	http://www.itbobo.com/wordpress-plug-ins-to-develop-a-detailed-tutorial.html
 *  http://wordpress.org/plugins/search-and-replace/
 */

define('REGEX_REPLACE_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once REGEX_REPLACE_PATH . 'regex-replace-function.php';

register_activation_hook(__FILE__, 'regex_replace_install'); 
register_deactivation_hook(__FILE__, 'regex_replace_remove');  
add_action('admin_menu','regex_replace_menu');
 

