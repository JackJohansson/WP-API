<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Cache;

	/**
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class Option extends Model {

		/**
		 * Holds an instance of the class.
		 *
		 * @var \App\Models\Option $this
		 */
		private static self $instance;

		/**
		 * Holds an array of loaded options
		 *
		 * @var array $options
		 */
		private static array $options;

		/**
		 * Name of the options table.
		 *
		 * @var string $table
		 */
		protected $table = 'options';

		/**
		 * Set up the options.
		 *
		 * @param array $attributes
		 */
		public function __construct( array $attributes = [] ) {

			if ( isset( self::$instance ) ) {
				return;
			}

			self::$instance = $this;

			parent::__construct( $attributes );
		}

		/**
		 * Parse an option and return its value.
		 *
		 * @param string $section
		 * @param string $name
		 * @param bool   $default
		 *
		 * @return mixed
		 */
		public static function getOption( string $section, string $name, $default = FALSE ) {
			if ( ! isset( static::$options ) ) {
				try {
					// If the options are cached
					self::$options = Cache::rememberForever(
						'wp_api_options',
						function () {
							return @json_decode(
								static::$instance->where( 'option_name', '=', 'wp-api-options' )->first()->option_value,
								TRUE,
								512,
								JSON_THROW_ON_ERROR
							);
						}
					);
				} catch ( \Exception $exception ) {
					self::$options = [];
				}
			}

			return self::$options[ $section ][ $name ] ?? $default;
		}

		/**
		 * Return a custom table name, if set.
		 *
		 * @return string
		 */
		public function get_table(): string {
			if ( defined( 'WP_API_OPTIONS_TABLE' ) ) {
				return WP_API_OPTIONS_TABLE;
			}

			return $this->table;
		}

		/**
		 * Check if the options have been loaded.
		 *
		 * @return bool
		 */
		public function optionsLoaded(): bool {
			return isset( self::$instance );
		}
	}
