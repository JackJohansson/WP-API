<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Relations\HasOne;

	/**
	 * Class to interact with the attachment post type.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class Attachment extends GeneralPost {

		protected $hidden = [ 'ID', 'laravel_through_key' ];

		/**
		 * Get the path to an uploaded media.
		 *
		 * @return string
		 */
		public function attachedFile(): string {

			// Get the path to the attachment
			$attached_file = $this->metaData()->where( 'meta_key', '=', '_wp_attached_file' );

			// If there's a value
			if ( $attached_file ) {
				return (string) $attached_file->meta_value;
			}

			return '';
		}

		protected static function boot() {
			parent::boot();

			// Set the post type
			static::addGlobalScope(
				'setPostType',
				function ( Builder $builder ) {
					$builder->where( 'post_type', '=', 'attachment' );
				}
			);
		}

		/**
		 * Get the thumbnails for this attachment.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasOne
		 */
		public function thumbnails(): HasOne {
			return $this->hasOne( PostMeta::class, 'post_id', 'ID' )
			            ->where( 'meta_key', '=', '_wp_attachment_metadata' );
		}
	}
