<?php
	$target = 'SINGLE';
?>
<div class="wrap">
	
	<h2 class='opt-title'><span id='icon-options-general' class='fcm-options'>
		<img src="<?php echo plugins_url('wp-fcm/images/wp-fcm-logo.png');?>" alt=""></span>
		<?php echo __( 'FCM Dashboard', 'fcm' ); ?>
	</h2>
	
	<?php
	if(isset($_POST['send_now'])){ 
		$title 		= $_POST['notif_title'];
		$content 	= $_POST['notif_content'];
		$target 	= $_POST['target'];
		$s_regid 	= $_POST['s_reg_id'];
		$check_box	= ($target == 'SINGLE') ? 'block' : 'none';
		fcm_notif_submit($title, $content, $target, $s_regid);
	} 
	?>
	
	<form name="notif_form" action="" id="notif_form" method="post"> 

		<div class="postbox">
		<div class="inside">
		
		<h3><?php _e('Message For Push Notifications','wp_fcm'); ?></h3>

		<table>
			<tr>
				<td style="width:150px;"><?php _e('Message Title')?> </td>
				<td><input style="width:300px;" type="text" name="notif_title" value="<?php echo $title; ?>"></td>
			</tr>
			<tr>
				<td><?php _e('Message Content')?> </td>
				<td>
					<textarea style="width:300px;" id="notif_content" name="notif_content" type="text" rows="5"><?php echo $content; ?></textarea><br>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><i><?php _e('*Please don\'t use HTML','px_gcm'); ?></i><br><br><br></td>
			</tr>
			
			<tr>
				<td><?php _e('Target')?></td>		
				<td>
					<form action="">
						<input type="radio" name="target" value="SINGLE" <?php echo checked('SINGLE', $target, false ); ?> > <?php _e('Single Device'); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="target" value="ALL" <?php echo checked('ALL', $target, false ); ?> > <?php _e('All Device'); ?>
					</form>
				</td>			
			<tr>
			
			<tr>
				<td><?php _e('Device RegId')?> </td>
				<td><input style="width:500px; <?php echo 'display:'.$check_box; ?>" id="s_reg_id" type="text" name="s_reg_id" disable="true" value="<?php echo $s_regid; ?>"></td>
			</tr>
			
		</table> 	  
		
		</div>
		</div>
		
		<input type="submit" value="Send Now" name="send_now" id="send_now" class="button button-primary">
	</form>
</div>

<script>
	var radios = document.querySelectorAll('input[type=radio][name="target"]');
	function changeHandler(event) {
		if ( this.value === 'SINGLE' ) {
			document.getElementById('s_reg_id').style.display = 'block';
		} else {
			document.getElementById('s_reg_id').style.display = 'none';
			document.getElementById('s_reg_id').value  = '';
		}
	}
	Array.prototype.forEach.call(radios, function(radio) {
	   radio.addEventListener('change', changeHandler);
	});
	
</script>