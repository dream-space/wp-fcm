<?php 
/* * 
 * All the functions for the settings page
 */
function fcm_setting_init() {
	add_settings_section('fcm_setting-section', '', 'fcm_setting_section_callback', 'wp-fcm');
	
	add_settings_field('fcm-api-key', __('FCM API KEY','wp_fcm'), 'fcm_setting_apikey_callback', 'wp-fcm', 'fcm_setting-section');
	add_settings_field('post-new', __('When Add New Post','wp_fcm'), 'fcm_setting_post_new_callback', 'wp-fcm', 'fcm_setting-section');
	add_settings_field('post-update', __('When Update Post','wp_fcm'), 'fcm_setting_post_update_callback', 'wp-fcm', 'fcm_setting-section');

    add_settings_field('post-new-title', __('New Post Title','wp_fcm'), 'fcm_setting_post_new_title_callback', 'wp-fcm', 'fcm_setting-section');
    add_settings_field('post-update-title', __('Update Post Title','wp_fcm'), 'fcm_setting_post_update_title_callback', 'wp-fcm', 'fcm_setting-section');

	register_setting('wp-fcm-settings-group', 'fcm_setting', 'fcm_setting_validate' );
}

function fcm_setting_section_callback() { }

function fcm_setting_apikey_callback() {
	$options = get_option('fcm_setting');
    $html = '<input type="text" name="fcm_setting[fcm-api-key]" size="50" value="'. $options['fcm-api-key'] .'" /> <hr/>';
    echo $html;
}

function fcm_setting_post_new_callback(){
	$options = get_option('fcm_setting');
	$html = '<input type="checkbox" id="post-new" name="fcm_setting[post-new]" value="1"' . checked( 1, $options['post-new'], false ) . '/>';
	echo $html;
}

function fcm_setting_post_update_callback(){
	$options = get_option('fcm_setting');
	$html= '<input type="checkbox" id="post-update" name="fcm_setting[post-update]" value="1"' . checked( 1, $options['post-update'], false ) . '/>';
	echo $html;
}

function fcm_setting_post_new_title_callback() {
    $options = get_option('fcm_setting');
    $val = !fcm_tools_is_empty($options['post-new-title']) ? $options['post-new-title'] : 'New Post';
    $html = '<input type="text" name="fcm_setting[post-new-title]" size="50" value="'. $val .'" />';
    echo $html;
}

function fcm_setting_post_update_title_callback() {
    $options = get_option('fcm_setting');
    $val = !fcm_tools_is_empty($options['post-update-title']) ? $options['post-update-title'] : 'Update Post';
    $html = '<input type="text" name="fcm_setting[post-update-title]" size="50" value="'. $val .'" /> <hr/>';
    echo $html;
}

function fcm_setting_validate($arr_input) {
	$options = get_option('fcm_setting');
	$options['fcm-api-key'] = trim( $arr_input['fcm-api-key'] );

	$options['post-new'] = trim( $arr_input['post-new'] );
	$options['post-update'] = trim( $arr_input['post-update'] );
    $options['post-new-title'] 	= trim( $arr_input['post-new-title'] );
    $options['post-update-title'] = trim( $arr_input['post-update-title'] );

	$options['post-scheduled'] = trim( $arr_input['post-scheduled'] );
	$options['dialog-confirm-notif'] = trim( $arr_input['dialog-confirm-notif'] );
	return $options;
}
?>