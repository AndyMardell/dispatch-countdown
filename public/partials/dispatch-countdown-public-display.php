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
	<div class="container">
		<div class="dispatch-countdown__inner">
			<?php do_action( 'dispatch_countdown_prepend_main_content' ); ?>
			<p>
				<?php echo esc_html( $wording ); ?>
				<span
					class="dispatch-countdown__time"
					id="dispatch-countdown__time"
					data-for="<?php echo esc_attr( $product_id ); ?>"
				>
					<?php echo esc_html( $countdown ); ?>
				</span>
			</p>
			<?php do_action( 'dispatch_countdown_append_main_content' ); ?>
		</div>
	</div>
</div>
