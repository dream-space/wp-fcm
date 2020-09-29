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

    add_settings_field('notif-topic', __('Send Notif by Topic','wp_fcm'), 'fcm_setting_topic_callback', 'wp-fcm', 'fcm_setting-section');

    add_settings_field('use-security', __('Use Security','wp_fcm'), 'fcm_setting_use_security_callback', 'wp-fcm', 'fcm_setting-section');
    add_settings_field('security-code', __('SECURITY CODE','wp_fcm'), 'fcm_setting_security_callback', 'wp-fcm', 'fcm_setting-section');

	register_setting('wp-fcm-settings-group', 'fcm_setting', 'fcm_setting_validate' );
}

function fcm_setting_section_callback() { }

function fcm_setting_apikey_callback() {
    $options = fcm_main_get_option();
    $html = '<input type="text" name="fcm_setting[fcm-api-key]" size="50" value="'. $options['fcm-api-key'] .'" /> <hr/>';
    echo $html;
}

function fcm_setting_post_new_callback(){
    $options = fcm_main_get_option();
	$html = '<input type="checkbox" id="post-new" name="fcm_setting[post-new]" value="1"' . checked( 1, $options['post-new'], false ) . '/>';
	echo $html;
}

function fcm_setting_post_update_callback(){
    $options = fcm_main_get_option();
	$html= '<input type="checkbox" id="post-update" name="fcm_setting[post-update]" value="1"' . checked( 1, $options['post-update'], false ) . '/>';
	echo $html;
}

function fcm_setting_post_new_title_callback() {
    $options = fcm_main_get_option();
    $html = '<input type="text" name="fcm_setting[post-new-title]" size="50" value="'. $options['post-new-title'] .'" />';
    echo $html;
}

function fcm_setting_post_update_title_callback() {
    $options = fcm_main_get_option();
    $html = '<input type="text" name="fcm_setting[post-update-title]" size="50" value="'. $options['post-update-title'] .'" /> <hr/>';
    echo $html;
}

function fcm_setting_topic_callback(){
    $options = fcm_main_get_option();
    $html = '<input type="checkbox" id="notif-topic" name="fcm_setting[notif-topic]" value="1"' . checked( 1, $options['notif-topic'], false ) . '/>';
    echo $html;
}


function fcm_setting_use_security_callback(){
    $options = fcm_main_get_option();
    $html = '<input type="checkbox" id="use-security" name="fcm_setting[use-security]" value="1"' . checked( 1, $options['use-security'], false ) . '/>';
    echo $html;
}

function fcm_setting_security_callback() {
    $options = fcm_main_get_option();
    $html = '<textarea name="fcm_setting[security-code]" id="ping_sites" class="large-text code" rows="2">'.$options['security-code'].'</textarea>';
    echo $html;
}

function fcm_setting_validate($arr_input) {
	$options = get_option('fcm_setting');
	$options['fcm-api-key'] = trim( $arr_input['fcm-api-key'] );

	$options['post-new'] = trim( $arr_input['post-new'] );
	$options['post-update'] = trim( $arr_input['post-update'] );
    $options['post-new-title'] 	= trim( $arr_input['post-new-title'] );
    $options['post-update-title'] = trim( $arr_input['post-update-title'] );

    $options['notif-topic'] = trim( $arr_input['notif-topic'] );

    $options['use-security'] = trim( $arr_input['use-security'] );
    $options['security-code'] = trim( $arr_input['security-code'] );

	return $options;
}
?>