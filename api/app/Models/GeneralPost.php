<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;

	/**
	 * Model to interact with the posts table.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class GeneralPost extends Model {

		/**
		 * Attributes to be cast.
		 *
		 * @var string[] $casts
		 */
		protected $casts = [
			'post_author'   => 'integer',
			'comment_count' => 'integer',
			'menu_order'    => 'integer'
		];

		/**
		 * Attributes hidden by default.
		 *
		 * @var string[] $hidden
		 */
		protected $hidden = [
			'ping_status',
			'post_password',
			'to_ping',
			'pinged',
			'post_content_filtered',
			'menu_order',
			'post_type',
			'post_mime_type',
			'post_status',
			'comment_status',
			'post_parent',
			'post_author',
			'pivot',
			'meta_value',
			'meta_key',
			'meta_id',
			'post_id',
			'keyword_id',
			'id'
		];

		/**
		 * Name of the posts table.
		 *
		 * @var string $table
		 */
		protected $table = 'posts';

		/**
		 * Get the author of this post.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
		 */
		public function author(): BelongsTo {
			return $this->belongsTo( User::class, 'post_author', 'ID' );
		}

		/**
		 * Unserialize the thumbnail column, if joined.
		 *
		 * @param $value
		 *
		 * @return array|mixed
		 */
		public function getThumbnailsAttribute( $value ) {

			if ( ! empty( $value ) ) {
				// Try to unserialize
				$metadata = @unserialize( $value, [ 'allowed_classes' => FALSE ] );

				return $metadata[ 'sizes' ] ?? [];
			}

			return new \stdClass();
		}

		/**
		 * Return a custom table name, if set.
		 *
		 * @return string
		 */
		public function get_table(): string {
			if ( defined( 'WP_API_POSTS_TABLE' ) ) {
				return WP_API_POSTS_TABLE;
			}

			return $this->table;
		}

		/**
		 * Get all the metadata for this post.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasMany
		 */
		public function metaData(): HasMany {
			return $this->hasMany( PostMeta::class, 'post_id', 'ID' );
		}
	}
