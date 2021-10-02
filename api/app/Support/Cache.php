<?php

	namespace App\Support;

	use Illuminate\Support\Collection;
	use Illuminate\Support\Facades\Cache as SystemCache;

	/**
	 * Class used to perform various actions on
	 * the cache system.
	 *
	 */
	class Cache {
		/**
		 * A list of valid cache keys.
		 *
		 * @var array|string[] $caches
		 */
		private static array $caches = [
			'posts_search_cache',
			'pages_search_cache',
			'attachments_search_cache',
			'users_search_cache'
		];

		/**
		 * Clear a specific cache item.
		 *
		 * @param string $name
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public static function clear( string $name ): bool {
			return cache()->forget( $name );
		}

		/**
		 * Add an item to the cache.
		 *
		 * @param string   $key
		 * @param string   $item
		 * @param \Closure $closure
		 *
		 * @return mixed
		 */
		public static function getOrSet( string $key, string $item, \Closure $closure ) {
			// If the key doesn't exist
			$cache = self::get( $key, $item );

			// Check if the item exists in the cache
			if ( $cache instanceof Collection ) {
				// If it's set
				$value = $cache;
			} else {
				// Create the cache key
				$value = $closure();
				SystemCache::put( $key, [ $item => $value ] );
			}

			return $value;
		}

		/**
		 * Get a nested item from the cache.
		 *
		 * @param string $key
		 * @param string $item
		 *
		 * @return mixed|void
		 */
		public static function get( string $key, string $item ) {
			// If the key exists
			if ( SystemCache::has( $key ) ) {

				$items = SystemCache::get( $key );

				// If item also exists
				if ( isset( $items[ $item ] ) ) {
					return $items[ $item ];
				}
			}

			return NULL;
		}
	}