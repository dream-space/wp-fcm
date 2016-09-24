<?php
	$fcm_table_logs = new Fcm_Table_Logs_List();
	if( isset($_POST['s']) ){
		$fcm_table_logs->prepare_items($_POST['s']);
	} else {
		$fcm_table_logs->prepare_items('');
	}
?>
<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='fcm-options'>
		<img src="<?php echo plugins_url('wp-fcm/images/wp-fcm-logo.png');?>" alt=""></span>
		<?php echo __( 'FCM Logs History', 'fcm' ); ?>
	</h2>
	
	<!--Search box display -->
	<form action="" method="post">
    <?php 
		$fcm_table_logs->search_box( __( 'Search' ), 'fcm' ); 
		foreach ($_POST as $key => $value) { 
			if( 's' !== $key ) echo("<input type='hidden' name='$key' value='$value' />");
		}
	?>
	</form>
	
	<!--Table view display -->
	<form id="fcm_Logs_table_list" action="" method="get">
		<!-- To ensure that the form posts back to our current page -->
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Render table display -->
		<?php $fcm_table_logs->display(); ?>
	</form>
	
</div>