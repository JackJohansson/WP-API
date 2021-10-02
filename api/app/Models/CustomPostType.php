<?php

	namespace App\Models;

	use App\Helper\Traits\HasFeaturedImage;

	/**
	 * CLass to handle custom post types.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class CustomPostType extends GeneralPost {
		use HasFeaturedImage;
	}
