<?php

	namespace WP_API;

	/**
	 * Class used to handle the plugin's options.
	 *
	 */
	class Setting {

		/**
		 * An array of loaded options.
		 *
		 * @var array $options
		 */
		private static array $options;

		/**
		 * Generate a new token on demand.
		 *
		 * @return string
		 */
		public static function generateToken(): string {
			// Validate the request
			if ( ! wp_verify_nonce( $_POST[ 'wp_api_generate_token_nonce' ], 'wp-api-generate-token' ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json(
					[
						'status'  => FALSE,
						'message' => esc_html__( 'Sorry, you do not have permission to perform this task.', 'wp-api' )
					]
				);
			}

			// If successfully generated a token
			if ( Kernel::generateToken() ) {
				wp_send_json( [ 'status' => TRUE ] );
			}

			// Failed.
			wp_send_json(
				[
					'status'  => FALSE,
					'message' => esc_html__( 'An unknown error occurred while trying to regenerate the token. Please try again later.', 'wp-api' )
				]
			);
		}

		/**
		 * Get an option by its name.
		 *
		 * @param string $section
		 * @param string $name
		 * @param mixed  $default
		 *
		 * @return mixed
		 */
		public static function getOption( string $section, string $name, $default = FALSE ) {
			$options = static::allOptions();

			return $options[ $section ][ $name ] ?? $default;
		}

		/**
		 * Decode and return the options.
		 *
		 * @return array
		 */
		private static function allOptions(): array {

			// If options have already been loaded
			if ( isset( self::$options ) ) {
				return self::$options;
			}

			$options = get_option( 'wp-api-options', '' );

			try {
				return self::$options = @json_decode( $options, TRUE, 512, JSON_THROW_ON_ERROR );
			} catch ( \Exception $exception ) {
				return [];
			}
		}

		/**
		 * Delete the plugin options.
		 *
		 * @return bool
		 */
		public static function removeOptions(): bool {
			return delete_option( 'wp-api-options' );
		}

		/**
		 * Update the plugin's general options.
		 *
		 */
		public static function saveGeneralOptions(): void {
			// Check permissions
			static::validateSave( 'wp-api-save-general-options', 'wp_api_general_options_nonce' );

			// Get the current settings
			$options = static::allOptions();

			// Valid rate units
			$valid_units = [ 'min', 'hour', 'day' ];

			$options[ 'general' ][ 'enable' ]     = isset( $_POST[ 'wp_api_enable' ] ) && 'on' === $_POST[ 'wp_api_enable' ];
			$options[ 'general' ][ 'rate_count' ] = ( 1 <= (int) $_POST[ 'wp_api_rate' ] && (int) $_POST[ 'wp_api_rate' ] <= 60 ) ? (int) $_POST[ 'wp_api_rate' ] : 60;
			$options[ 'general' ][ 'rate_unit' ]  = FALSE !== ( $unit = array_search( $_POST[ 'wp_api_rate_unit' ], $valid_units, TRUE ) ) ? $valid_units[ $unit ] : 'min';

			$options[ 'general' ][ 'throttle' ] = 'on' === $_POST[ 'wp_api_throttle' ];

			// Save options
			static::saveAll( $options );

			// Redirect to previous page
			wp_safe_redirect( admin_url( 'admin.php?page=wp-api-settings' ) );
			exit();
		}

		/**
		 * Check the permissions and verify the nonce.
		 *
		 * @param string $nonce
		 * @param string $nonce_key
		 */
		private static function validateSave( string $nonce, string $nonce_key ): void {
			// Check permissions
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you do not have permissions to perform this action.', 'wp-api' ) );
			}

			// Check referrer
			check_admin_referer( $nonce, $nonce_key );
		}

		/**
		 * Save the options.
		 *
		 * @param array $options
		 *
		 * @return bool
		 */
		private static function saveAll( array $options ): bool {
			try {
				// Try to save the options
				$result = update_option( 'wp-api-options', json_encode( $options, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE ), TRUE );
				// Clear the options cache
				if ( $result ) {
					return Kernel::callCacheApi( 'wp_api_options' );
				}
			} catch ( \Exception $exception ) {
				return FALSE;
			}

			return FALSE;
		}

		/**
		 * Update the plugin's search options.
		 *
		 */
		public static function saveSearchOptions(): void {
			// Check permissions
			static::validateSave( 'wp-api-save-search-options', 'wp_api_search_options_nonce' );

			// Get the current settings
			$options = static::allOptions();

			$options[ 'search' ][ 'enable' ]       = isset( $_POST[ 'wp_api_enable_search' ] ) && 'on' === $_POST[ 'wp_api_enable_search' ];
			$options[ 'search' ][ 'cache' ]        = isset( $_POST[ 'wp_api_enable_search_cache' ] ) && 'on' === $_POST[ 'wp_api_enable_search_cache' ];
			$options[ 'search' ][ 'autogenerate' ] = isset( $_POST[ 'wp_api_enable_autogenerate' ] ) && 'on' === $_POST[ 'wp_api_enable_autogenerate' ];

			// Get a list of registered post types
			$cpt         = get_post_types( [ 'public' => TRUE, '_builtin' => TRUE ] );
			$enabled_cpt = isset( $_POST[ 'wp_api_search_post_types' ] ) && is_array( $_POST[ 'wp_api_search_post_types' ] ) ? $_POST[ 'wp_api_search_post_types' ] : [];

			// Get the valid custom post types
			$options[ 'search' ][ 'post_types' ] = array_intersect( array_values( $cpt ), $enabled_cpt );

			// Save options
			static::saveAll( $options );

			// Redirect to previous page
			wp_safe_redirect( admin_url( 'admin.php?page=wp-api-search' ) );
			exit();
		}

		/**
		 * Set the plugin's default options on
		 * activation.
		 *
		 */
		public static function setDefaultOptions(): void {
			// If options are already set
			if ( FALSE !== get_option( 'wp-api-options', FALSE ) ) {
				return;
			}

			// An array of default options
			$options = [
				'general' => [
					'enable'     => FALSE,
					'throttle'   => TRUE,
					'rate_count' => 60,
					'rate_unit'  => 'min'
				],
				'search'  => [
					'enable'       => FALSE,
					'cache'        => FALSE,
					'autogenerate' => FALSE,
					'post_types'   => []
				]
			];

			// Save
			static::saveAll( $options );
		}
	}