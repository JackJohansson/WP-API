<?php

	namespace WP_API;

	/**
	 * Class used to generate the templates used
	 * by the plugin.
	 *
	 */
	class Template {
		/**
		 * Get the template to render the home
		 * link.
		 *
		 * @return string
		 */
		public static function adminHome(): string {
			return require( plugin_dir_path( WP_API_PLUGIN_FILE ) . '/templates/admin/admin-home.php' );
		}

		/**
		 * Get the template used to output the
		 * admin search panel.
		 *
		 * @return string
		 */
		public static function adminSearch(): string {
			return require( plugin_dir_path( WP_API_PLUGIN_FILE ) . '/templates/admin/admin-search.php' );
		}

		/**
		 * Get the template used to output the
		 * admin setting page.
		 *
		 * @return string
		 */
		public static function adminSetting(): string {
			return require( plugin_dir_path( WP_API_PLUGIN_FILE ) . '/templates/admin/admin-setting.php' );
		}

		/**
		 * Render a notice for the admin.
		 *
		 * @param string $message
		 * @param string $class
		 * @param bool   $dismissible
		 */
		public static function renderAdminNotice( string $message, string $class = 'notice notice-success', bool $dismissible = TRUE ): void {
			printf( '<div class="%1$s %2$s"><p>%3$s</p></div>', esc_attr( $class ), $dismissible ? 'is-dismissible' : '', esc_html( $message ) );
		}
	}