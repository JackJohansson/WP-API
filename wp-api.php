<?php
	/**
	 * Plugin Name:       WP API
	 * Plugin URI:        https://github.com/JackJohansson/WP-API
	 * Description:       A fast and light API for WordPress.
	 * Version:           1.0.0
	 * Requires at least: 5.2
	 * Requires PHP:      7.4
	 * Author:            Jack Johansson
	 * Author URI:        https://jackjohansson.com
	 * License:           GPL v2 or later
	 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
	 * Update URI:        https://
	 * Text Domain:       https://github.com/JackJohansson/WP-API
	 * Domain Path:       /languages
	 */

	// Include autoloader
	require_once( __DIR__ . '/vendor/autoload.php' );

	const WP_API_PLUGIN_FILE = __FILE__;
	const WP_API_PLUGIN_DIR  = __DIR__;
	const WP_API_VERSION     = 1.0;

	\WP_API\Kernel::init();