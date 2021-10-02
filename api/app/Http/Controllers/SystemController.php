<?php

	namespace App\Http\Controllers;

	use App\Models\Option;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Http\Request;

	/**
	 * Class used to handle endpoints for the system
	 * actions.
	 *
	 */
	class SystemController extends Controller {
		/**
		 * Clear a specific cache.
		 *
		 *
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return \Illuminate\Http\JsonResponse
		 */
		public function clearCache( Request $request ): JsonResponse {
			$token = $request->post( 'wp_api_token' );
			$cache = $request->post( 'cache_item' );

			// Validate the token.
			$token_value = Option::where( 'option_name', '=', 'wp-api-token' )->first();

			// If token is invalid
			if ( NULL === $token_value || $token !== $token_value->option_value ) {
				return response()->json(
					[
						'success' => FALSE,
						'error'   => TRUE,
						'message' => __( 'Invalid token.' )
					],
					403
				);
			}

			// Clear the cache
			try {
				$result = \App\Support\Cache::clear( $cache );
			} catch ( \Exception $e ) {
				return response()->json( [ 'success' => FALSE, 'error' => TRUE, 'message' => $e->getMessage() ], 500 );
			}

			return response()->json( [ 'success' => $result, 'error' => ! $result ] );
		}
	}
