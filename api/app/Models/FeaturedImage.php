<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Builder;

	/**
	 * Model responsible for handling featured images.
	 *
	 */
	class FeaturedImage extends GeneralPost {

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


	}
