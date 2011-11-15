<?php
/*
Plugin Name: Way2SMS WordPress plugin
Plugin URI: https://github.com/ashfame/Way2SMS-WordPress-plugin
Description: Adds a dashboard widget in WordPress to send a SMS/text message using credentials of your Way2SMS account.
Author: Ashfame
Version: 0.1
Author URI: http://blog.ashfame.com
*/

/**
 * Function to add the Way2SMS dashboard widget
 */

add_action( 'wp_dashboard_setup', 'w2s_dashboard_widget' );

function w2s_dashboard_widget() {
	wp_add_dashboard_widget( 'way2sms-wp-plugin', 'Way2SMS (Send a text message)', 'w2s_dashboard_widget_content', 'w2s_dashboard_widget_setup' );
}


/**
 * Function that shows the content of the way2sms dashboard widget
 */

function w2s_dashboard_widget_content() {
	$way2sms = get_option( 'way2sms' );
	if ( $way2sms ) {
		// If its a POST submit, send message
		if ( isset( $_POST['way2sms_recipient'] ) && isset( $_POST['way2sms_message'] ) ) {
			require 'way2sms-api.php';
			$result = sendWay2SMS( $way2sms['username'] , $way2sms['password'] , $_POST['way2sms_recipient'] , $_POST['way2sms_message'] );
		}
?>
		<style type="text/css">
			#way2sms-wp-plugin input[type="text"], #way2sms-wp-plugin textarea {
				margin-bottom:5px;
				width:97%;
			}
			#way2sms-wp-plugin label {
				margin-bottom:5px;
				display:inline-block;
			}
			#way2sms-wp-plugin em {
				display:block;
			}
		</style>
		<form action="" method="POST">
			<label for="way2sms_recipient">Recipient's mobile no</label>
			<br />
			<input type="text" name="way2sms_recipient" id="way2sms_recipient" width="500" />
			<em>(Separate multiple nos with comma)</em>
			<br />
			<label for="way2sms_message">Message</label><br />
			<textarea name="way2sms_message" id="way2sms_message" rows="10" cols="60"></textarea>
			<br />
			<input type="submit" class="button-primary" id="way2sms-submit" value="Send SMS" />
		</form>
<?php
	} else {
		echo '<p>Hover over the title of this widget, click on configure link on the right side and enter your Way2SMS credentials</p>';
	}
	echo '<div id="message"><pre>';
	print_r( $result );
	foreach ( $result as $r ) {
		if ( $r['result'] )
			echo "<li>Message to {$r['phone']} was sent successfully!</li>";
		else
			echo "<li>Error sending message to {$r['phone']}</li>";
	}
	echo '</pre></div>';
}


/**
 * Function that show the configure screen of the way2sms dashboard widget
 */

function w2s_dashboard_widget_setup() {
	
	// Save the data
	if ( isset( $_POST['widget_id'] ) && ( $_POST['widget_id'] == 'way2sms-wp-plugin' ) )
		update_option( 'way2sms', array( 'username' => $_POST['way2sms_username'], 'password' => $_POST['way2sms_password'] ) );
	
	// Build the form
	$way2sms = get_option( 'way2sms' );
?>
	<style type="text/css">
		#way2sms-wp-plugin .dashboard-widget-control-form label {
			width: 70px;
			display: block;
			float: left;
			padding-top:5px;
		}
	</style>
	<label for="way2sms_username">Username</label>
	<input type="text" name="way2sms_username"  id="way2sms_username" value="<?php echo $way2sms['username']; ?>" />
	<br />
	<label for="way2sms_password">Password</label>
	<input type="password" name="way2sms_password"  id="way2sms_password" value="<?php echo $way2sms['password']; ?>" />
	
<?php
}
