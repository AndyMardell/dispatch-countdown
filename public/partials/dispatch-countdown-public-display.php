<?php
/**
 * Public-facing view for the plugin
 *
 * @link       mardell.me
 * @since      1.0.0
 *
 * @package    Dispatch_Countdown
 * @subpackage Dispatch_Countdown/public/partials
 */

defined( 'ABSPATH' ) || exit;

do_action( 'dispatch_countdown_before_main_content' );

?>

<div class="dispatch-countdown" id="dispatch-countdown">
	<?php
	echo wp_kses_post( $dispatch_countdown_content );
	?>
</div>
