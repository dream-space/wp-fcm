<?php

/*
* All the functions push notification to Android
*/

/* Handle submit notif from dashboard page */
function fcm_notif_submit($title, $content, $target, $single_regid){
    $valid_title 	= true;
    $valid_conten 	= true;
    $valid_target 	= true;
    $total 	        = 1;

    if(fcm_tools_is_empty($title)){
        fcm_tools_error_msg("Message Title Cannot empty.");
        $valid_title = false;
    }

    if(fcm_tools_is_empty($content)){
        fcm_tools_error_msg("Message Content Cannot empty.");
        $valid_conten = false;
    }

    if($target === "SINGLE"){
        if(fcm_tools_is_empty($single_regid)){
            fcm_tools_error_msg("Device RegId Cannot Empty for Single Device.");
            $valid_conten = false;
        }
    } elseif($target === "ALL"){
        $total = fcm_data_get_all_count();
        if($total <= 0){
            fcm_tools_error_msg("You have no fcm user.");
            return;
        }
        $single_regid = "";
    }

    if($valid_title && $valid_conten && $valid_target){
        $message = array( 'title' => $title, 'content' => $content , 'post_id' => -1 );
        $respon  = fcm_notif_divide_send($single_regid, $total, $message);

        if($respon['success'] === NULL || $respon['failure'] === NULL){
            fcm_tools_error_msg("Make sure your FCM API KEY is correct.");
            return;
        }

        $res_msg = '<p>Success : '.$respon['success']. '<br>Failure : '.$respon['failure'].'</p>';
        fcm_tools_success_msg($res_msg);
        fcm_data_insert_log($title, $content, $target, "CUSTOM_DASHBOARD", $respon['success'], $respon['failure']);
    }
}

/*
* Send push notification add/update post
*/
function fcm_notif_post($new_status, $old_status, $post) {
    $post_title = get_the_title($post);
    $post_id 	= $post->ID;
    $content    = $post_title;
    $title      = "";

    $options = get_option('fcm_setting');
    $is_send_notif = false;

    // on add post
    if ($old_status != 'publish' && $new_status == 'publish' && 'post' == get_post_type($post) && $options['post-new'] != false) {
        $is_send_notif = true;
        $title = !fcm_tools_is_empty($options['post-new-title']) ? $options['post-new-title'] : 'New Post';
        $event = "NEW_POST";

    } else if ($old_status == 'publish' && $new_status == 'publish' && 'post' == get_post_type($post) && $options['post-update'] != false) { // on update post
        $is_send_notif = true;
        $title 	 = !fcm_tools_is_empty($options['post-update-title']) ? $options['post-update-title'] : 'Update Post';
        $event = "UPDATE_POST";

    }

    if($is_send_notif == true){
        $message = array(
            'title'     => $title,
            'content'   => $content,
            'post_id'   => $post_id,
            'image'     => get_post_image_thumb($post)[0]
        );

        $total = fcm_data_get_all_count();
        if($total <= 0) return;

        $respon  = fcm_notif_divide_send("", $total, $message);
        if($respon['success'] === NULL || $respon['failure'] === NULL) return;

        fcm_data_insert_log($title, $content, "ALL", $event, $respon['success'], $respon['failure']);
    }
}

/*
 * Get image thumbnail if available
 */
function get_post_image_thumb($post){
    $image = array();
    if (has_post_thumbnail($post->ID)){
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
    }
    return $image;
}


/*
 * Handle notification more than 1000 users
 */
function fcm_notif_divide_send($reg_id, $total, $message) {
    $reg_ids = array();
    $push_status = array();
    if ($reg_id != "") {
        array_push($reg_ids, $reg_id);
        $push_status[] = fcm_notif_send($reg_ids, $message);
    } else {
        $page = floor($total / 1000);
        for ($i = 0; $i <= $page; $i++){
            $regid_arr = fcm_data_get_regid_by_page(1000, ($i * 1000));
            // send notification per 1000 items
            $push_status[] = fcm_notif_send($regid_arr, $message);
        }
    }

    $success_count = 0;
    $failure_count = 0;
    foreach($push_status as $s){
        if(!empty($s['success'])) $success_count = $success_count + $s['success'];
        if(!empty($s['failure'])) $failure_count = $failure_count + ($s['failure']);
    }

    $obj_data = array();
    $obj_data['success'] = $success_count;
    $obj_data['failure'] = $failure_count;
    return $obj_data;
}


function fcm_notif_send($registatoin_ids, $message) {
    $error = false;

    //echo '<pre>'; print_r($result); echo '</pre>';

    //Get Option
    $fcm_api_key=get_option('fcm_setting')['fcm-api-key'];
    if(empty($fcm_api_key) || strlen($fcm_api_key) <= 0) {
        $error = true;
        return $error;
    }

    $url = 'https://fcm.googleapis.com/fcm/send';
    //$url = 'https://android.googleapis.com/gcm/send';

    $fields = array(
        'registration_ids' => $registatoin_ids,
        'data' => $message
    );

    $headers = array( 'Authorization: key=' . $fcm_api_key, 'Content-Type: application/json' );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) { die('Curl failed: ' . curl_error($ch)); }
    // Close connection
    curl_close($ch);
    $result_data = json_decode($result, true);
    return $result_data;

}

?>