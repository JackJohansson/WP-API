<?php

	namespace App\Helper\Traits;

	use App\Models\Attachment;
	use App\Models\PostMeta;
	use Illuminate\Database\Eloquent\Relations\HasOneThrough;

	/**
	 * Trait to load a featured image for a post.
	 *
	 */
	trait HasFeaturedImage {

		/**
		 * Get the featured image for this post.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
		 */
		public function featuredImage(): HasOneThrough {
			return $this->hasOneThrough( Attachment::class, PostMeta::class, 'post_id', 'ID', 'ID', 'meta_value' )
			            ->select( [ 'ID' ] )
			            ->with( 'thumbnails' );
		}
	}