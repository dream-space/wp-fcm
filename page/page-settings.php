<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='fcm-options'>
		<img src="<?php echo plugins_url('wp-fcm/images/wp-fcm-logo.png');?>" alt=""></span>
		<?php echo __( 'FCM Settings', 'fcm' ); ?>
	</h2>
	
	<?php if( isset($_GET['settings-updated']) ) { ?>
	<div id="message" class="updated">
		<p><strong><?php _e('Settings saved','wp_fcm') ?></strong></p>
	</div>
	<?php } ?>
	
	<div class="postbox">
	<div class="inside">
	<form method="post" action="options.php">
		<?php settings_fields('wp-fcm-settings-group'); ?>
		<?php do_settings_sections('wp-fcm'); ?>
		<?php submit_button(); ?>
	</form>
	</div>
	</div>
</div>