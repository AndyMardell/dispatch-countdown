<?php
/**
 * Provide a admin area view for the plugin
 *
 * @link       mardell.me
 * @since      1.0.0
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/admin/partials
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="dispatch-countdown-settings wrap">
	<h1>Dispatch Countdown Settings</h1>
	<form method="post" action="options.php">
		<?php
			settings_fields( 'dispatch_countdown_settings_fields' );
			do_settings_sections( 'dispatch-countdown-admin' );
			submit_button();
		?>
	</form>
</div>
