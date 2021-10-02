<?php

	namespace App\Support;

	/**
	 *
	 */
	class Configurator {
		// Configuration file path
		private const WP_CONFIG = __DIR__ . '/../../../../../../wp-config.php';

		// Arrays of options to be loaded
		private const OPTIONS = [
			'DB_NAME'                => 'DB_DATABASE',
			'DB_USER'                => 'DB_USERNAME',
			'DB_PASSWORD'            => 'DB_PASSWORD',
			'DB_HOST'                => 'DB_HOST',
			'DB_PORT'                => 'DB_PORT',
			'WP_API_APP_KEY'         => 'APP_KEY',
			'WP_API_APP_DEBUG'       => 'APP_DEBUG',
			'WP_API_APP_URL'         => 'APP_URL',
			'WP_API_MEMCACHED_HOST'  => 'MEMCACHED_HOST',
			'WP_API_REDIS_HOST'      => 'REDIS_HOST',
			'WP_API_REDIS_PASSWORD'  => 'REDIS_PASSWORD',
			'WP_API_REDIS_PORT'      => 'REDIS_PORT',
			'WP_API_DB_PREFIX'       => 'DB_PREFIX',
			'WP_API_OVERRIDE_CONFIG' => 'WP_API_OVERRIDE_CONFIG',
		];

		/**
		 * Parsed options.
		 *
		 * @var array $config
		 */
		private static array $config = [];

		/**
		 * Try to parse the config file and set the options.
		 *
		 */
		public function __construct() {
			try {
				$this->parseConfig();

				// If config is enabled
				if ( isset( self::$config[ 'WP_API_OVERRIDE_CONFIG' ] ) && 'TRUE' === strtoupper( self::$config[ 'WP_API_OVERRIDE_CONFIG' ] ) ) {
					$this->setOptions();
				}
			} catch ( \Exception $exception ) {
				throw new \http\Exception\InvalidArgumentException( $exception->getMessage() );
			}
		}

		/**
		 * Parse the config file and set the options.
		 *
		 * @throws \Exception
		 */
		private function parseConfig(): void {
			// Config already loaded
			if ( ! empty( self::$config ) ) {
				return;
			}

			// No config file found
			if ( ! file_exists( self::WP_CONFIG ) || FALSE === ( $config = file_get_contents( self::WP_CONFIG ) ) ) {
				throw new \Exception( __( 'Configuration file does not exist or can not be opened.' ), 'missing_config_file' );
			}
			// Parse options
			$parse = preg_match_all(
				'~define\((?<keys>\'[^\']*\'|\"[^\"]*\"),(?<values>\'[^\']*\'|\"[^\"]*\"|true|false|TRUE|FALSE)\);~iU',
				str_replace( [ " ", "\t", "\n", "\r", "\0", "\x0B" ], '', $config ),
				$matches
			);

			if ( FALSE === $parse || 0 === $parse ) {
				throw new \Exception( __( 'Could not parse the configuration file.' ), 'invalid_config_file' );
			}

			// Set the options
			foreach ( $matches[ 'keys' ] as $key => $match ) {
				// Single quoted value
				if ( 0 === strpos( $matches[ 'values' ][ $key ], "'" ) ) {
					// @formatter:off
					self::$config[ str_replace( [ '"', "'" ], '', $match ) ] = str_replace( "'", '', $matches[ 'values' ][ $key ] );
					// @formatter:on
				} else {
					// @formatter:off
					self::$config[ str_replace( [ '"', "'" ], '', $match ) ] = str_replace( '"', '', $matches[ 'values' ][ $key ] );
					// @formatter:on
				}
			}

		}

		/**
		 * Set the required options.
		 *
		 */
		private function setOptions(): void {
			foreach ( self::$config as $key => $value ) {
				if ( isset( self::OPTIONS[ $key ] ) ) {
					putenv( self::OPTIONS[ $key ] . '=' . $value );
				}
			}
		}
	}

	new Configurator();