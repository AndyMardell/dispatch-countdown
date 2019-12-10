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

								if ( ! res.content || res.content === 'false') {
									return $( '#dispatch-countdown' ).hide()
								}

								$timer.text( res.content )
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
