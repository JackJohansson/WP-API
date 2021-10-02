<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;

	/**
	 * Class to query post meta.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class PostMeta extends Model {

		/**
		 * Hide the columns as this is just a pivot table.
		 *
		 * @var string[] $hidden
		 */
		protected $hidden = [ 'meta_key', 'post_id', 'meta_id' ];

		/**
		 * Name of the postmeta table.
		 *
		 * @var string $table
		 */
		protected $table = 'postmeta';

		/**
		 * Unserialize the thumbnails if that's the case.
		 *
		 * @param $value
		 *
		 * @return mixed
		 */
		public function getMetaValueAttribute( $value ) {

			if ( '_wp_attachment_metadata' === $this->meta_key ) {
				// Try to unserialize
				$metadata = @unserialize( $value, [ 'allowed_classes' => FALSE ] );

				return $metadata[ 'sizes' ] ?? [];
			}

			return [];
		}

		/**
		 * Return a custom table name, if set.
		 *
		 * @return string
		 */
		public function get_table(): string {
			if ( defined( 'WP_API_POSTMETA_TABLE' ) ) {
				return WP_API_POSTMETA_TABLE;
			}

			return $this->table;
		}

		/**
		 * Get the post that owns this metadata.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
		 */
		public function post(): BelongsTo {
			return $this->belongsTo( GeneralPost::class, 'post_id', 'ID' );
		}
	}
