<?php

	namespace App\Models;

	use App\Helper\Traits\HasFeaturedImage;
	use Illuminate\Database\Eloquent\Builder;

	/**
	 * Class used to interact with the pages.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class Page extends GeneralPost {
		use HasFeaturedImage;

		protected static function boot() {
			parent::boot();

			// Set the post type
			static::addGlobalScope(
				'setPostType',
				function ( Builder $builder ) {
					$builder->where( 'post_type', '=', 'page' );
				}
			);
		}
	}
