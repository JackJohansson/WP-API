<?php

	namespace WP_API;

	/**
	 * Core class.
	 */
	final class Kernel {

		/**
		 * An instance of the class.
		 *
		 * @var \WP_API\Kernel $this
		 */
		private static self $kernel;

		/**
		 * Set up the plugin.
		 */
		private function __construct() {
			$this->registerInstallationHooks();
			$this->registerAdminHooks();
			$this->registerContentHooks();
			$this->registerScripts();
			$this->registerAjaxHooks();
		}

		/**
		 * Register the hooks to be run on plugin activation,
		 * deactivation and uninstallation.
		 */
		private function registerInstallationHooks(): void {
			register_activation_hook( WP_API_PLUGIN_FILE, [ $this, 'onActivation' ] );
			register_deactivation_hook( WP_API_PLUGIN_FILE, [ $this, 'onDeactivation' ] );
			register_uninstall_hook( WP_API_PLUGIN_FILE, [ $this, 'onUninstallation' ] );
		}

		/**
		 * Register the hooks used by the admin panel.
		 *
		 */
		private function registerAdminHooks(): void {
			// Register the menus
			Menu::init();
			// Register crons
			add_action( 'init', [ $this, 'registerCron' ] );
		}

		/**
		 * Register various hooks required by the plugin.
		 *
		 */
		private function registerContentHooks(): void {
			Search::init();
		}

		/**
		 * Register the styles and scripts used by the plugin.
		 *
		 */
		private function registerScripts(): void {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminScripts' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminStyles' ] );
		}

		/**
		 * Register the ajax endpoints used by the plugin.
		 *
		 */
		private function registerAjaxHooks(): void {
			add_action( 'admin_post_wp_api_save_general_options', [ Setting::class, 'saveGeneralOptions' ] );
			add_action( 'admin_post_wp_api_save_search_options', [ Setting::class, 'saveSearchOptions' ] );
			add_action( 'admin_post_wp_api_generate_token', [ Setting::class, 'generateToken' ] );
		}

		/**
		 * Call the API and clear the cache for this type.
		 *
		 * @param string $type
		 *
		 * @return bool
		 */
		public static function callCacheApi( string $type ): bool {

			// Get the API token
			$token = get_option( 'wp-api-token' );

			// No token generated.
			if ( FALSE === $token ) {
				return FALSE;
			}

			// Ping the API
			$result = wp_remote_post(
				self::getEndpointUrl( 'clear-cache' ),
				[
					'method' => 'POST',
					'body'   => [
						'wp_api_token' => $token,
						'cache_item'   => $type
					]
				]
			);

			return is_wp_error( $result );
		}

		/**
		 * Get the endpoint url for a named endpoint.
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		public static function getEndpointUrl( string $name ): string {
			// An array of available routes
			$endpoints = [
				'search-posts'       => 'api/public/search/posts/',
				'search-pages'       => 'api/public/search/pages/',
				'search-attachments' => 'api/public/search/attachments/',
				'search-cpt'         => 'api/public/search/cpt/',
				'clear-cache'        => 'api/public/system/clear-cache',
			];

			return isset( $endpoints[ $name ] ) ? trailingslashit( plugin_dir_url( WP_API_PLUGIN_FILE ) ) . $endpoints[ $name ] : '';
		}

		/**
		 * Enqueue the admin scripts.
		 *
		 * @param $hook
		 */
		public function enqueueAdminScripts( $hook ): void {

			// Register the scripts
			$this->registerAdminScripts( $hook );

			// Enqueue if we're on plugin page
			if ( is_admin() && in_array( $hook, $this->pluginPages(), TRUE ) ) {
				wp_enqueue_script( 'wp-api-bootstrap' );
				wp_enqueue_script( 'wp-api-touchspin' );
				wp_enqueue_script( 'wp-api-select2' );
				wp_enqueue_script( 'wp-api-scripts' );
			}

			// Editor scripts
			if ( is_admin() && in_array( $hook, [ 'post.php', 'post-edit.php' ], TRUE ) ) {
				wp_enqueue_script( 'wp-api-editor' );
			}

		}

		/**
		 * Register the plugin's scripts.
		 *
		 * @param $hook
		 */
		public function registerAdminScripts( $hook ): void {

			wp_register_script(
				'wp-api-bootstrap',
				plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/js/bootstrap.bundle.min.js',
				[ 'jquery' ],
				'4.3.1',
				TRUE
			);

			wp_register_script(
				'wp-api-select2',
				plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/js/select2.full.min.js',
				[
					'jquery',
					'wp-api-bootstrap'
				],
				'4.0.6',
				TRUE
			);

			wp_register_script(
				'wp-api-touchspin',
				plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/js/jquery.bootstrap-touchspin.min.js',
				[ 'jquery', ],
				'4.2.5',
				TRUE
			);

			wp_register_script(
				'wp-api-editor',
				plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/js/editor.js',
				[ 'jquery', ],
				'1.0.0',
				TRUE
			);

			wp_register_script(
				'wp-api-scripts',
				plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/js/scripts.js',
				[
					'jquery',
					'wp-api-bootstrap',
					'wp-api-select2'
				],
				'1.0',
				TRUE
			);
		}

		/**
		 * A list of page slugs registered by this plugin.
		 *
		 * @return string[]
		 */
		private function pluginPages(): array {
			return [
				'toplevel_page_wp-api-settings',
				'wp-api_page_wp-api-settings',
				'wp-api_page_wp-api-search',
			];
		}

		/**
		 * Enqueue the admin styles.
		 *
		 * @param $hook
		 */
		public function enqueueAdminStyles( $hook ): void {
			// Register the styles
			$this->registerAdminStyles( $hook );

			// Enqueue if we're on plugin page
			if ( is_admin() && in_array( $hook, $this->pluginPages(), TRUE ) ) {
				wp_enqueue_style( 'wp-api-la' );
				wp_enqueue_style( 'wp-api-bs-select' );
				wp_enqueue_style( 'wp-api-touchspin' );
				wp_enqueue_style( 'wp-api-select2' );
				wp_enqueue_style( 'wp-api-css' );
				wp_enqueue_style( 'wp-api-global' );
			}

			// Editor styles
			if ( is_admin() && in_array( $hook, [ 'post.php', 'post-edit.php' ], TRUE ) ) {
				wp_enqueue_style( 'wp-api-editor-css' );
			}
		}

		/**
		 * Enqueue the plugin's styles.
		 *
		 * @param $hook
		 */
		public function registerAdminStyles( $hook ): void {
			wp_register_style( 'wp-api-bs-select', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/css/bootstrap-select.min.css', NULL, '1.13.5' );
			wp_register_style( 'wp-api-touchspin', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/css/jquery.bootstrap-touchspin.min.css', NULL, '4.2.5' );
			wp_register_style( 'wp-api-select2', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/css/select2.min.css', NULL, '4.0.6' );
			wp_register_style( 'wp-api-la', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/font/line-awesome/css/line-awesome.css', NULL, '1.1' );
			wp_register_style( 'wp-api-css', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/css/styles.css', NULL, 1.0 );
			wp_register_style( 'wp-api-global', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/css/global.css', NULL, 1.0 );
			wp_register_style( 'wp-api-editor-css', plugin_dir_url( WP_API_PLUGIN_FILE ) . 'assets/css/editor.css', NULL, 1.0 );
		}

		/**
		 * Boot the plugin and register the requirements.
		 *
		 */
		public static function init(): void {
			if ( ! isset( self::$kernel ) ) {
				self::$kernel = new Kernel();
			}
		}

		/**
		 * Tasks to be performed on plugin activation.
		 *
		 */
		public function onActivation(): void {
			Database::createTables();
			Kernel::generateToken();
			Setting::setDefaultOptions();
		}

		/**
		 * Generate a new token for the system.
		 *
		 * @return bool
		 */
		public static function generateToken(): bool {
			// Try to generate a secure token
			try {
				$token = password_hash( bin2hex( random_bytes( 16 ) ), PASSWORD_DEFAULT );
			} catch ( \Exception $exception ) {
				$token = password_hash( uniqid( '', TRUE ), PASSWORD_DEFAULT );
			}

			return update_option( 'wp-api-token', $token, FALSE );
		}

		/**
		 * Tasks to be performed on deactivation of
		 * the plugin.
		 *
		 */
		public function onDeactivation(): void {
			Database::removeTables();
		}

		/**
		 * Register tasks to be performed upon
		 * uninstallation.
		 *
		 */
		public function onUninstallation(): void {
			Database::removeTables();
			Kernel::removeOptions();
			Setting::removeOptions();
		}

		/**
		 * Remove the plugin options on plugin uninstallation.
		 *
		 */
		private static function removeOptions(): void {
			$options = [ 'wpapi-token', 'wpapi-settings' ];

			foreach ( $options as $option ) {
				delete_option( $option );
			}
		}

		/**
		 * Callback to register the plugin's cronjobs.
		 */
		public function registerCron(): void {
			// Cron job to clean the unused keywords
			if ( ! wp_next_scheduled( 'wp_api_cleanup_keywords' ) ) {
				wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'wp_api_cleanup_keywords' );
			}
		}
	}