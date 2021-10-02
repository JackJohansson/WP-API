<?php

	use App\Http\Controllers\PostController;
	use App\Http\Controllers\SystemController;
	use Illuminate\Support\Facades\Route;

	// API for internal actions
	Route::post( 'system/clear-cache', [ SystemController::class, 'clearCache' ] );

	// Search API for posts
	Route::get( 'search/posts/{keyword}', [ PostController::class, 'searchPostsPublic' ] )->name( 'search.posts' );
	Route::get( 'search/pages/{keyword}', [ PostController::class, 'searchPagesPublic' ] )->name( 'search.pages' );
	Route::get( 'search/attachments/{keyword}', [ PostController::class, 'searchAttachmentsPublic' ] )->name( 'search.attachments' );
	Route::get( 'search/cpt/{cpt}/{keyword}', [ PostController::class, 'searchCptPublic' ] )->name( 'search.cpt' );

	// Invalid route
	Route::fallback(
		function () {
			return [
				'success' => FALSE,
				'error'   => TRUE,
				'message' => __( 'The requested route does not exist on this server.' )
			];
		}
	);