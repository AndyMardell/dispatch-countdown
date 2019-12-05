/* global jQuery, dispatch_countdown */

(function ($) {
	'use strict'

	$( document ).ready(
		function () {
			var $timer = $( '#dispatch-countdown__time' )

			if ( ! $timer.length) {
				return false
			}

			setInterval(
				function () {
					$.ajax(
						{
							url: dispatch_countdown.ajax_url,
							type: 'POST',
							data: {
								action: 'get_countdown',
								nonce: dispatch_countdown.nonce,
								product: $timer.data( 'for' )
							},
							success: function (data) {
								var res = JSON.parse( data )

								if (res.content && res.content !== 'false') {
									$timer.text( res.content )
								} else {
									$( '#dispatch-countdown' ).hide()
								}
							},
							error: function () {
								$( '#dispatch-countdown' ).hide()
							}
						}
					)
				},
				60000
			)
		}
	)
})( jQuery )
