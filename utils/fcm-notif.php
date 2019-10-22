<?php

/*
* All the functions push notification to Android
*/

const NOTIFICATION_TOPIC = "ALL-DEVICE";

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

        if($respon == NULL){
            fcm_tools_error_msg("Make sure your FCM API KEY is correct.");
            return;
        }

        $res_msg = '<p>Success : '.$respon['status']. '<br>Message : '.$respon['msg'].'</p>';
        fcm_tools_success_msg($res_msg);
        fcm_data_insert_log($title, $content, $target, "CUSTOM_DASHBOARD", $respon['status']);
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
    if ($old_status != 'publish' && $new_status == 'publish' && $post->post_type == 'post' && $options['post-new'] != false) {
        $is_send_notif = true;
        $title = !fcm_tools_is_empty($options['post-new-title']) ? $options['post-new-title'] : 'New Post';
        $event = "NEW_POST";

    } else if ($old_status == 'publish' && $new_status == 'publish' && $post->post_type == 'post' && $options['post-update'] != false) { // on update post
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

        fcm_data_insert_log($title, $content, "ALL", $event, $respon['status']);
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
    $options = get_option('fcm_setting');

    $data = array( 'registration_ids' => null, 'to' => null, 'data' => $message );

    if($reg_id != ""){
        $data['to'] = $reg_id;
        $push_response = fcm_notif_send($data);
    } else if ($options['notif-topic'] == true) {
        $data['to'] = "/topics/" . NOTIFICATION_TOPIC;
        $push_response = fcm_notif_send($data);
    } else {
        $page = floor($total / 1000);
        for ($i = 0; $i <= $page; $i++){
            $regid_arr = fcm_data_get_regid_by_page(1000, ($i * 1000));
            // send notification per 1000 items
            $data['registration_ids'] = $regid_arr;
            $push_response = fcm_notif_send($data);
        }
    }

    $resp = array('status' => 'SUCCESS', 'msg' => 'Notification sent successfully');
    if ($reg_id != "" && isset($push_response['results'][0]['error'])){
        $resp['msg'] = $push_response['results'][0]['error'];
        $resp['status'] = 'SUCCESS';
    }
    return $resp;
}


function fcm_notif_send($data) {
    $error = false;

    //Get Option
    $fcm_api_key=get_option('fcm_setting')['fcm-api-key'];
    if(empty($fcm_api_key) || strlen($fcm_api_key) <= 0) {
        $error = true;
        return $error;
    }

    $url = 'https://fcm.googleapis.com/fcm/send';

    $headers = array( 'Authorization: key=' . $fcm_api_key, 'Content-Type: application/json' );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporary
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute post
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

?>