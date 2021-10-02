<?php

	namespace WP_API;

	/**
	 * Class used to provide search data for
	 * the API.
	 *
	 */
	class Search {
		/**
		 * Holds an instance of the class.
		 *
		 * @var \WP_API\Search $search
		 */
		private static Search $search;

		/**
		 * Setup the search API.
		 */
		private function __construct() {
			$this->registerContentHooks();
		}

		/**
		 * Register the callback functions to be fired
		 * on content update.
		 */
		public function registerContentHooks(): void {
			add_action( 'save_post', [ $this, 'postInserted' ], 10, 3 );
			add_action( 'attachment_updated', [ $this, 'attachmentUpdated' ], 10, 3 );
			add_action( 'add_attachment', [ $this, 'attachmentAdded' ], 10, 1 );
			add_action( 'add_meta_boxes', [ $this, 'registerMetaBoxes' ] );
			add_action( 'wp_api_cleanup_keywords', [ $this, 'cleanupKeywordTable' ] );
		}

		/**
		 * When an attachment is inserted.
		 *
		 * @param int $post_ID
		 */
		public function attachmentAdded( int $post_ID ): void {
			// Check permissions
			if ( ! current_user_can( 'edit_post', $post_ID ) ) {
				return;
			}

			// Get the post object
			$attachment = get_post( $post_ID );

			if ( NULL !== $attachment ) {
				$this->syncKeywords( $this->calculateKeywords( $attachment ), $attachment, $post_ID );
			}
		}

		/**
		 * Sync a list of keywords for a given post.
		 *
		 * @param array    $keywords
		 * @param \WP_Post $post
		 * @param int      $post_ID
		 */
		public function syncKeywords( array $keywords, \WP_Post $post, int $post_ID ): void {
			try {
				// Insert the values.
				$ids = $this->insertKeywords( $keywords );
				// Sync the pivot
				$this->syncRelations( $post_ID, $ids );
				// Call the API to clear the cache
				Kernel::callCacheApi( $post->post_type . 's_search_cache' );
			} catch ( \Exception $e ) {
				// Throw an error for the admin
				//wp_send_json(
				//	[
				//		'code'              => 'wp_die',
				//		'message'           => $e->getMessage(),
				//		'data'              => [ 'status' => 500 ],
				//		'additional_errors' => []
				//	],
				//	500
				//);
			}
		}

		/**
		 * Generate a list of keywords for a given post.
		 *
		 * @param \WP_Post $post
		 *
		 * @return array
		 */
		public function calculateKeywords( \WP_Post $post ): array {
			// An array of keywords to be inserted
			$keywords = [];

			// Get the keywords
			if ( isset( $_POST[ 'wp_api_search_keywords' ] ) && is_array( $_POST[ 'wp_api_search_keywords' ] ) ) {
				$keywords = array_filter( array_unique( array_map( 'sanitize_text_field', array_map( 'trim', (array) $_POST[ 'wp_api_search_keywords' ] ) ) ) );
			}

			// Generate automatic keywords based on the title.
			if ( TRUE === Setting::getOption( 'search', 'autogenerate' ) ) {
				$keywords = array_merge( explode( ' ', $post->post_title ), $keywords );
			}

			// Merge the keywords and remove the propositions
			$keywords = array_unique( array_diff( $keywords, $this->listPropositions() ) );

			// If there's something left to insert
			if ( empty( $keywords ) ) {
				return [];
			}

			return $keywords;
		}

		/**
		 * Insert an array of keywords into the keyword table.
		 *
		 * @param array $keywords
		 *
		 * @return array
		 * @throws \Exception
		 */
		private function insertKeywords( array $keywords ): array {
			global $wpdb;

			$table = Database::DB_PREFIX . Database::KEYWORDS_TABLE;

			if ( $keywords ) {
				// Get an array of existing keywords
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT `id` FROM `{$wpdb->prefix}{$table}` WHERE `value` IN ( " . implode( ', ', array_fill( 0, count( $keywords ), '%s' ) ) . " );",
						$keywords
					),
					ARRAY_A
				);
			} else {
				$results = [];
			}

			// Get the existing columns
			$inserts = array_column( $results, 'id' );

			// Insert each item into database
			foreach ( $keywords as $keyword ) {
				$insert_id = $wpdb->query( $wpdb->prepare( "INSERT INTO `{$wpdb->prefix}{$table}` (`value`) VALUES ( %s ) ON DUPLICATE KEY UPDATE `id` = `id`;", $keyword ) );
				if ( $insert_id ) {
					$inserts[] = $wpdb->insert_id;
				}
			}

			// An error occurred.
			if ( empty( $inserts = array_filter( $inserts ) ) ) {
				throw new \Exception( esc_html__( 'Could not insert data into database.', 'wp-api' ) );
			}

			return $inserts;
		}

		/**
		 * Sync an array of keyword ids with a post ID.
		 *
		 * @param int   $post_id
		 * @param array $keyword_ids
		 *
		 * @return bool
		 * @throws \Exception
		 */
		private function syncRelations( int $post_id, array $keyword_ids ): bool {
			global $wpdb;

			$table = Database::DB_PREFIX . Database::KEYWORDS_PIVOT_TABLE;

			// A list of ids to be inserted
			$values = [];

			foreach ( $keyword_ids as $id ) {
				$values [] = "( {$post_id}, " . (int) $id . " )";
			}

			// Delete the old keys
			$delete = $wpdb->delete( $wpdb->prefix . $table, [ 'post_id' => $post_id ] );

			// Error deleting records
			if ( FALSE === $delete ) {
				throw new \Exception( esc_html__( 'Could not delete the old pivot records.', 'wp-api' ) );
			}

			// Insert the new records
			$result = $wpdb->query( "INSERT INTO `{$wpdb->prefix}{$table}` (`post_id`, `keyword_id`) VALUES " . implode( ', ', $values ) . ";" );

			// Nothing inserted
			if ( FALSE === $result ) {
				throw new \Exception( esc_html__( 'Could not create relations between keywords and the post.', 'wp-api' ) );
			}

			return TRUE;
		}

		/**
		 * A list of words to be excluded from the search.
		 *
		 * @return string[]
		 */
		private function listPropositions(): array {
			return [
				'at',
				'but',
				'by',
				'for',
				'on',
				'in',
				'off',
				'of',
				'up',
				'the',
				'to',
				'the',
				'thus',
				'though',
			];
		}

		/**
		 * When an attachment is updated.
		 *
		 * @param int      $post_ID
		 * @param \WP_Post $post_after
		 * @param \WP_Post $post_before
		 */
		public function attachmentUpdated( int $post_ID, \WP_Post $post_after, \WP_Post $post_before ): void {
			// Check permissions
			if ( ! current_user_can( 'edit_post', $post_ID ) ) {
				return;
			}

			// No save on auto-saves and revisions
			if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
				return;
			}

			// If it's an update
			if ( 'publish' !== $post_after->post_status ) {
				return;
			}

			$this->syncKeywords( $this->calculateKeywords( $post_after ), $post_after, $post_ID );
		}

		/**
		 * Delete the unused keywords.
		 *
		 * @return bool
		 */
		public static function cleanupKeywordTable(): bool {
			global $wpdb;

			$keyword_table = Database::DB_PREFIX . Database::KEYWORDS_TABLE;
			$pivot_table   = Database::DB_PREFIX . Database::KEYWORDS_PIVOT_TABLE;

			$result = $wpdb->query( "DELETE FROM `{$wpdb->prefix}{$keyword_table}` WHERE `id` NOT IN ( SELECT DISTINCT `keyword_id` FROM `{$wpdb->prefix}{$pivot_table}`);" );

			return FALSE === $result;
		}

		/**
		 * Create a new instance if not already
		 * set.
		 *
		 */
		public static function init(): void {
			if ( ! isset( self::$search ) ) {
				self::$search = new Search();
			}
		}

		/**
		 * Actions performed when a post is created.
		 *
		 * @param int      $post_ID
		 * @param \WP_Post $post
		 * @param bool     $update
		 */
		public function postInserted( int $post_ID, \WP_Post $post, bool $update ): void {
			// Check permissions
			if ( ! isset( $_POST[ 'wp_api_post_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'wp_api_post_nonce' ], 'wp-api-save-post' ) || ! current_user_can( 'edit_post', $post_ID ) ) {
				return;
			}

			// No save on auto-saves and revisions
			if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
				return;
			}

			// If it's an update
			if ( 'publish' !== $post->post_status ) {
				return;
			}

			$this->syncKeywords( $this->calculateKeywords( $post ), $post, $post_ID );
		}

		/**
		 * Register the plugin's metaboxes.
		 *
		 */
		public function registerMetaBoxes(): void {
			// Keyword metabox
			add_meta_box(
				'wp-api-keywords',
				esc_html__( 'WP-API Keywords', 'wp-api' ),
				[
					$this,
					'renderKeywordMetabox'
				],
				get_post_types( [ 'public' => TRUE, '_builtin' => TRUE ] ),
				'side',
			);
		}

		/**
		 * Render the metabox used to generate keywords.
		 *
		 */
		public function renderKeywordMetabox(): void {
			// Get the current post and its keywords
			$keywords = static::getKeywords( (int) get_the_ID() );
			?>
			<label for="wp-api-keyword-field" class="components-form-token-field__label"><?php esc_html_e( 'Add a new keyword', 'wp-api' ); ?></label>
			<div class="components-form-token-field__input-container" id="wp-api-keyword-wrapper">
				<?php
					if ( $keywords ) {
						foreach ( $keywords as $keyword ) { ?>
							<span class="components-form-token-field__token wp-api-keyword-pill" data-value="<?php echo esc_attr( $keyword[ 'value' ] ); ?>">
								<span class="components-form-token-field__token-text" id="wp-api-single-keyword-<?php echo esc_attr( $keyword[ 'keyword_id' ] ); ?>">
									<span aria-hidden="true"><?php echo esc_html( $keyword[ 'value' ] ) ?></span>
								</span>
								<button
										type="button"
										aria-describedby="wp-api-single-keyword-<?php echo esc_attr( $keyword[ 'keyword_id' ] ); ?>"
										class="components-button components-form-token-field__remove-token has-icon wp-api-remove-keyword"
										aria-label="<?php esc_html_e( 'Remove Keyword', 'wp-api' ); ?>">
									<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
										<path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path>
									</svg>
								</button>
								<input type="hidden" name="wp_api_search_keywords[]" value="<?php echo esc_attr( $keyword[ 'value' ] ); ?>">
							</span>
							<?php
						}
					}
				?>

				<input
						id="wp-api-keyword-field"
						type="text"
						autocomplete="off"
						class="components-form-token-field__input"
						role="combobox"
						aria-expanded="false"
						aria-autocomplete="list"
						aria-describedby="wp-api-keyword-field"
						value=""
				>
			</div>
			<?php wp_nonce_field( 'wp-api-save-post', 'wp_api_post_nonce' ) ?>
			<p class="components-form-token-field__help"><?php esc_html_e( 'You can add multiple keywords to this post to be searchable via the API. Any whitespace in the keywords will be removed.', 'wp-api' ); ?></p>
			<?php
		}

		/**
		 * Get a list of keywords attached to a post.
		 *
		 * @param int $post_id
		 *
		 * @return array
		 */
		public static function getKeywords( int $post_id ): array {

			// Invalid ID
			if ( 0 === $post_id ) {
				return [];
			}

			global $wpdb;

			$keywords_table = Database::DB_PREFIX . Database::KEYWORDS_TABLE;
			$pivot_table    = Database::DB_PREFIX . Database::KEYWORDS_PIVOT_TABLE;
			$query          = "
				SELECT `pivots`.`id`,`keyword_id`, `keywords`.`value` 
				FROM `{$wpdb->prefix}{$pivot_table}` AS `pivots` 
				    LEFT JOIN `{$wpdb->prefix}{$keywords_table}` AS `keywords` ON `pivots`.`keyword_id` = `keywords`.`id` 
				WHERE `pivots`.`post_id` = {$post_id};";

			// Query a list of keywords attached to this post
			$results = $wpdb->get_results( $query, ARRAY_A );

			if ( $results ) {
				return $results;
			}

			return [];
		}
	}