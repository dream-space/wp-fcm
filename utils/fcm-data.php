<?php
/*
 * 	All the functions related database
 */


/* Transaction for Table Fcm Users -------------------------------------------
 */
function fcm_data_init_table_users(){
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';
    $charset_collate = $wpdb->get_charset_collate();
    $sql =
        "CREATE TABLE IF NOT EXISTS " . $fcm_table . " (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`regid` text,
		`serial` text,
		`device_name` text,
		`os_version` text,
		`created_at` bigint(30),
		PRIMARY KEY (`id`)
		) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function fcm_data_count_users($search){
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';
    $where 	= " ";
    if(!fcm_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(device_name, os_version) REGEXP '".$search."' ";
    }
    $sql = "SELECT COUNT(id) FROM ".$fcm_table." ".$where.";";
    return $wpdb->get_var($sql);
}

function fcm_data_get_users($orderby, $order, $per_page, $paged, $search){
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';
    $where 	= " ";
    if(!fcm_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(device_name, os_version) REGEXP '".$search."' ";
    }
    $sql 	= "SELECT * FROM ".$fcm_table." ".$where." ORDER BY ".$orderby." ".$order." LIMIT ".$per_page." OFFSET ".$paged;
    return $wpdb->get_results($sql, ARRAY_A);
}


function fcm_data_insert_user($regid, $device_name, $serial, $os_version) {
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';

    $device_name = !fcm_tools_is_empty($device_name) ? $device_name : '-';
    $serial 	 = !fcm_tools_is_empty($serial) ? $serial : '-';
    $os_version  = !fcm_tools_is_empty($os_version) ? $os_version : '-';
    $created_at  = time();

    $sql = "SELECT serial FROM ".$fcm_table." WHERE serial='".$serial."';";
    $result = $wpdb->get_results($sql);

    if (!$result) {
        $sql = "INSERT INTO ".$fcm_table." (regid, serial, device_name, os_version, created_at) 
				VALUES ('$regid', '$serial', '$device_name', '$os_version', $created_at)";
        return $wpdb->query($sql);
    } else {
        return $wpdb->update($fcm_table, array(
            'device_name' => $device_name,
            'os_version' => $os_version,
            'regid' => $regid,
            'created_at' => $created_at
        ),
            array('serial'=>$serial)
        );

    }
}

function fcm_data_get_all_regid() {
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';
    $arr_regid = array();
    $sql = "SELECT regid FROM ".$fcm_table;
    $res = $wpdb->get_results($sql);
    if ($res != false) {
        foreach($res as $row){
            array_push($arr_regid, $row->regid);
        }
    }
    return $arr_regid;
}

function fcm_data_get_regid_by_page($limit, $offset) {
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';
    $arr_regid = array();
    $sql = "SELECT regid FROM ".$fcm_table." ORDER BY id DESC LIMIT ".$limit." OFFSET ".$offset;
    $res = $wpdb->get_results($sql);
    if ($res != false) {
        foreach($res as $row){
            array_push($arr_regid, $row->regid);
        }
    }
    return $arr_regid;
}

function fcm_data_get_all_count() {
    global $wpdb;
    $fcm_table = $wpdb->prefix.'fcm_users';
    $sql = "SELECT COUNT(id) FROM ".$fcm_table;
    $res_count = $wpdb->get_var($sql);
    return $res_count;
}


/* Transaction for Table Fcm Logs --------------------------------------------------------
 */
function fcm_data_init_table_logs(){
    global $wpdb;
    $logs_table = $wpdb->prefix.'fcm_logs';
    $charset_collate = $wpdb->get_charset_collate();
    $sql =
        "CREATE TABLE IF NOT EXISTS " . $logs_table . " (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` text,
		`content` text,
		`target` text,
		`event` text,
		`success` int(11),
		`failure` int(11),
		`created_at` bigint(30),
		PRIMARY KEY (`id`)
		) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function fcm_data_insert_log($title, $content, $target, $event, $success, $failure){
    global $wpdb;
    $logs_table = $wpdb->prefix.'fcm_logs';
    $cur_time = time();
    $wpdb->insert($logs_table , array(
        'title' 	=> $title ,
        'content' 	=> $content,
        'target' 	=> $target,
        'event' 	=> $event,
        'success' 	=> $success,
        'failure' 	=> $failure,
        'created_at'=> $cur_time,
    ));
}

function fcm_data_count_logs($search){
    global $wpdb;
    $logs_table = $wpdb->prefix.'fcm_logs';
    $where 	= " ";
    if(!fcm_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(title, content, target, event, success, failure) REGEXP '".$search."' ";
    }
    $sql = "SELECT COUNT(id) FROM ".$logs_table." ".$where.";";
    return $wpdb->get_var($sql);
}

function fcm_data_get_logs($orderby, $order, $per_page, $paged, $search){
    global $wpdb;
    $logs_table = $wpdb->prefix.'fcm_logs';
    $where 	= " ";
    if(!fcm_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(title, content, target, event, success, failure) REGEXP '".$search."' ";
    }
    $sql = "SELECT * FROM ".$logs_table." ".$where." ORDER BY ".$orderby." ".$order." LIMIT ".$per_page." OFFSET ".$paged;
    return $wpdb->get_results($sql, ARRAY_A);
}

function fcm_data_delete_logs(){
    global $wpdb;
    $logs_table = $wpdb->prefix.'fcm_logs';
    $sql = "DROP TABLE IF EXISTS $logs_table;";
    return $wpdb->query($sql);
}


?>