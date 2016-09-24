<?php

/*
* All the functions push notification to Android
*/

/* Handle submit notif from dashboard page */
function fcm_notif_submit($title, $content, $target, $single_regid){
	$valid_title 	= true;
	$valid_conten 	= true;
	$valid_target 	= true;
	$all_regid 		= array();
	
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
		}else{
			array_push($all_regid, $single_regid);
		}
	} elseif($target === "ALL"){
		$all_regid = fcm_data_get_all_regid();
		if(count($all_regid) <= 0){
			fcm_tools_error_msg("You have no fcm user.");
			return;
		}
	}

	if($valid_title && $valid_conten && $valid_target){
		$message = array( 'title' => $title, 'content' => $content , 'post_id' => -1 );
		$respon  = fcm_notif_divide_send($all_regid, $message);
		
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
* Send push notification new post
*/
function fcm_notif_post_new($new_status, $old_status, $post) {
  $options = get_option('fcm_setting');
  if($options['post-new'] != false){
    if ($old_status != 'publish' && $new_status == 'publish' && 'post' == get_post_type($post)) {
		$post_title = get_the_title($post);
		$post_id 	= get_the_ID($post);
		
		$title 	 = "New Post";
		$content = $post_title;
		$message = array( 'title' => $title, 'content' => $content, 'post_id' => $post_id );
		
		$all_regid = fcm_data_get_all_regid();
		if(count($all_regid) <= 0) return;
		
		$respon  = fcm_notif_divide_send($all_regid, $message);
		if($respon['success'] === NULL || $respon['failure'] === NULL) return;
		
		fcm_data_insert_log($title, $content, "ALL", "NEW_POST", $respon['success'], $respon['failure']);
    }
   }
}

/*
*
* Send notification for post update
*
*/
function fcm_notif_post_update($new_status, $old_status, $post) {
  $options = get_option('fcm_setting');
  if($options['post-update'] != false){
    if ($old_status == 'publish' && $new_status == 'publish' && 'post' == get_post_type($post)) {
		$post_title = get_the_title($post);
		$post_id 	= get_the_ID($post);
		
		$title 	 = "Update Post";
		$content = $post_title;
		$message = array( 'title' => $title, 'content' => $content, 'post_id' => $post_id );
		
		$all_regid = fcm_data_get_all_regid();
		if(count($all_regid) <= 0) return;
		
		$respon  = fcm_notif_divide_send($all_regid, $message);
		if($respon['success'] === NULL || $respon['failure'] === NULL) return;
		
		fcm_data_insert_log($title, $content, "ALL", "UPDATE_POST", $respon['success'], $respon['failure']);
    }
   }
}


/* 
 * Handle notification more than 1000 users
 */
function fcm_notif_divide_send($all_reg_id, $message) {
	
	$gcm_reg_ids = array();
	$i = 0;
	// split gcm reg id per 1000 item
	foreach($all_reg_id as $reg_id){
		$i++;
		$gcm_reg_ids[floor($i/1000)][] = $reg_id;
	}
	// send notif per 1000 items
	$pushStatus = array();
	foreach($gcm_reg_ids as $divided_reg_id){
		$push_status[] = fcm_notif_send($divided_reg_id, $message);
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