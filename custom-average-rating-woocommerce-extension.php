<?php
/**
 * Custom Average Rating WooCommerce Extension
 * 
 * Displays the average rating from a custom API endpoint for WooCommerce.
 * 
 * @link              https://litcode.store/product/custom-average-rating-woocommerce-extension/
 * @since             1.0.0
 * @package           rafy
 * @author            Lit ✴ Code
 * @license           GPL-2.0-or-later
 * 
 * @wordpress-plugin
 * Plugin Name:       Custom Average Rating WooCommerce Extension
 * Plugin URI:        https://litcode.store/product/custom-average-rating-woocommerce-extension/
 * Description:       Displays the average rating from a custom API endpoint for WooCommerce.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Lit ✴ Code
 * Author URI:        https://litcode.store
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-average-rating
 * Domain Path:       /languages
 * Update URI:        https://github.com/RafyWP/custom-average-rating-woocommerce-extension
 * Network:           true
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @since 1.0.0
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function rafy_custom_average_rating_woocommerce_extension_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'rafy_custom_average_rating_woocommerce_extension_init' );
