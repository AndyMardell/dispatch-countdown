[![Build Status](https://travis-ci.com/AndyMardell/dispatch-countdown.svg?branch=master)](https://travis-ci.com/AndyMardell/dispatch-countdown)

# Dispatch Countdown

A plugin which allows you to display a countdown banner on your WooCommerce product pages.

| Contributors      | mardellme                                         |
| ----------------- | ------------------------------------------------- |
| Tags              | woocommerce, dispatch, countdown                  |
| Requires at least | 4.0                                               |
| Tested up to      | 5.3                                               |
| Requires PHP      | 5.6                                               |
| Stable tag        | 1.0.8                                             |
| License           | GPLv3 or later License                            |
| URI               | http://www.gnu.org/licenses/gpl-3.0.html          |
| Plugin URL        | https://wordpress.org/plugins/dispatch-countdown/ |

## Description

Specify a time to show (and start) the count down, and an end time to count down to.

Ideal for displaying a message such as "For same day dispatch, order within 1h 37m".

## Installation

You can download the latest release from the
[WordPress plugin page](https://wordpress.org/plugins/dispatch-countdown/)
or from the
[releases page](https://github.com/AndyMardell/dispatch-countdown/releases)
on GitHub.

1. Upload the plugin files to the `/wp-content/plugins/dispatch-countdown` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the WooCommerce->Settings->Product Settings->Dispatch Countdown screen to configure the plugin

## Moving the countdown

The countdown must be called at a point where `global $product` is available, so
make sure to call this plugin after WooCommerce has loaded. To move the
countdown, you can remove the action and then add it back wherever you want:

```php
/**
 * Move dispatch countdown
 */
function your_theme_move_dispatch_countdown() {
	// Check the class exists
	if ( ! class_exists( 'Dispatch_Countdown' ) ) {
		return;
	}

	// Get the current instance
	$dispatch_countdown = Dispatch_Countdown::get_public_instance();

	// Remove the action
	remove_action( 'woocommerce_before_single_product', array( $dispatch_countdown, 'display_countdown' ) );

	// Add the action back where you like
	add_action( 'woocommerce_before_main_content', array( $dispatch_countdown, 'display_countdown' ) );
}
add_action( 'init', 'your_theme_move_dispatch_countdown' );
```

Replacing `woocommerce_before_main_content` with whichever hook you wish

## Changing the HTML output

There are a few filters available to override certain parts of the output. The
main filter is `dispatch_countdown_content` and can be used as follows:

```php
/**
 * Change countdown HTML output
 *
 * NOTE: You must include an element with the id of `dispatch-countdown__time`
 * as javascript uses this to update the countdown.
 */
 function your_theme_dispatch_countdown_content ( $html, $wording, $product, $countdown ) {
 	$countdown_html  = esc_html( $wording );
 	$countdown_html .= '&nbsp;<span id="dispatch-countdown__time" data-for="' . esc_attr( $product ) . '">';
 	$countdown_html .= esc_html( $countdown );
 	$countdown_html .= '</span>';

 	return $countdown_html;
 }
 add_filter( 'dispatch_countdown_content', 'your_theme_dispatch_countdown_content', 10, 4 );
```

## Changelog

### 1.0.8

- Removes loader in favour of native WordPress add_filter/add_action
- Expose admin instance for devs
- Use prettier and eslint for JS development

### 1.0.7

- Update docs
- Add POT file for translations
- Adds filters to main output

### 1.0.6

- Allow dispatch countdown hook to be overridden
- Update documentation with how to move

### 1.0.5

- Remove some dist files

### 1.0.4

- No changes - deployment tests only

### 1.0.3

- No changes - deployment tests only

### 1.0.2

- Added icons
- Added screenshots

### 1.0.1

- Updated readme.txt to adhere to WordPress standards

### 1.0.0

- Initial release.
