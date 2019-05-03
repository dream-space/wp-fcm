<?php

/*
	Plugin Name: WP FCM
	Plugin URI: https://github.com/dream-space/wp-fcm/
	Description: Wordpress Plugin to manage and send Firebase Cloud Messaging for Android App. This plugin could send push notification to android user when add new post or update post.
	Version: 2.1
	Author: Dream Space
	Author URI: https://codecanyon.net/user/dream_space/portfolio
	License: GPLv3
	License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/*	
	Copyright (C) 2016  Dream Space (email : dev.dream.space@gmail.com)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define( 'ROOT_PATH', dirname(__FILE__) );
@include_once ROOT_PATH."/utils/fcm-setting.php";
@include_once ROOT_PATH."/utils/fcm-notif.php";
@include_once ROOT_PATH."/utils/fcm-tools.php";
@include_once ROOT_PATH."/utils/fcm-data.php";
@include_once ROOT_PATH."/utils/fcm-table.php";
@include_once ROOT_PATH."/utils/fcm-rest.php";

/** ------------------------ Registering All required action ------------------------ */

register_activation_hook( __FILE__, 'fcm_main_activation' );
register_deactivation_hook( __FILE__, 'fcm_main_deactivation' );

add_action('admin_menu', 'fcm_main_add_menu' );
add_action('admin_init', 'fcm_main_add_setting' );

add_filter('query_vars', 'fcm_main_query_vars', 0);
add_action('parse_request', 'fcm_main_parse_requests', 0);

add_action('transition_post_status', 'fcm_main_transition_post', 10, 3);
//add_action('future_to_publish', 'fcm_main_future_post_scheduled', 10, 1);

/** ------------------------ End of : Registering All required action ------------------- */


/* Actions when activation of plugin  */
function fcm_main_activation() {
	fcm_data_init_table_users();
	fcm_data_init_table_logs();
}

/* Actions when de-activation of plugin */
function fcm_main_deactivation() {
	fcm_data_delete_logs();
}

/* Add FCM menu at admin panel */
function fcm_main_add_menu() {
	add_menu_page('FCM', 'FCM', 'manage_options', 'wp-fcm-dashboard', 'fcm_tools_page_file_path', plugins_url('images/wp-fcm-logo.png', __FILE__) );

	add_submenu_page('wp-fcm-dashboard', 'WP FCM Dashboard',	'Dashboard', 	'manage_options',	'wp-fcm-dashboard', 'fcm_tools_page_file_path');
	add_submenu_page('wp-fcm-dashboard', 'WP FCM Users', 		'Users', 		'manage_options', 	'wp-fcm-users',   	'fcm_tools_page_file_path');
	add_submenu_page('wp-fcm-dashboard', 'WP FCM History', 		'History', 		'manage_options', 	'wp-fcm-history',   'fcm_tools_page_file_path');
	add_submenu_page('wp-fcm-dashboard', 'WP FCM Settings',  	'Settings', 	'manage_options', 	'wp-fcm-settings',   'fcm_tools_page_file_path');
}

/* Add setting scheme for setting page */
function fcm_main_add_setting(){
	fcm_setting_init();
}

/* Register query for registration API*/
function fcm_main_query_vars($vars){
	$vars[] = 'api-fcm';
	return $vars;
}

/**	Handle API Requests for registration user
 *  url     : http://www.domain-wp.com/wp-fcm=register
 *  type    : POST
 *  payload : JSON
 */
function fcm_main_parse_requests(){
	global $wp;
	if(isset($wp->query_vars['api-fcm'])){
		$api_fcm = $wp->query_vars['api-fcm'];
		if($api_fcm == 'register'){

			$fcm_rest = new Fcm_Rest();
			if($fcm_rest->get_request_method() != "POST") $fcm_rest->response('',406);
			$api_data 	 = json_decode(file_get_contents("php://input"), true);
			$regid 		 = $api_data['regid'];
			$serial 	 = $api_data['serial'];
			$device_name = $api_data['device_name'];
			$os_version  = $api_data['os_version'];

			if ($regid) {
				// insert POST request into database
				$res = fcm_data_insert_user($regid, $device_name, $serial, $os_version);
				if($res == 1){
					$data = json_encode(array('status'=> 'success', 'message'=>'successfully registered device'));
					$fcm_rest->response($data, 200);
				}else{
					$data = json_encode(array('status'=> 'failed', 'message'=>'failed when insert to database'));
					$fcm_rest->response($data, 200);
				}
			}else{
				$data = json_encode(array('status'=> 'failed', 'message'=>'regid cannot null'));
				$fcm_rest->response($data, 200);
			}
		} else if($api_fcm == 'info'){
			$fcm_rest = new Fcm_Rest();
			$data = json_encode(array('status'=> 'ok', 'wp_fcm_version'=>'1.0'));
			$fcm_rest->response($data, 200);
		}else{
			$data = array('status'=> 'failed', 'message'=>'Invalid Parameter');
			fcm_tools_respon_simple($data);
		}
	}
}

/* Handle notification when add/update post*/
function fcm_main_transition_post($new_status, $old_status, $post){
	fcm_notif_post($new_status, $old_status, $post);
}


?>
