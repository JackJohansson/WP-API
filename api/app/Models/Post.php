<?php

	namespace App\Models;

	use App\Helper\Traits\HasFeaturedImage;
	use Illuminate\Database\Eloquent\Builder;

	/**
	 *
	 * @mixin Builder
	 */
	class Post extends GeneralPost {
		use HasFeaturedImage;

		protected static function boot() {
			parent::boot();

			// Set the post type
			static::addGlobalScope(
				'setPostType',
				function ( Builder $builder ) {
					$builder->where( 'post_type', '=', 'post' );
				}
			);
		}

	}
