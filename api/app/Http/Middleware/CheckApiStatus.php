<?php

	namespace App\Http\Middleware;

	use Closure;
	use Illuminate\Http\Request;

	/**
	 * Check whether API is enabled or not.
	 *
	 */
	class CheckApiStatus {
		/**
		 * Handle an incoming request.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param \Closure                 $next
		 *
		 * @return mixed
		 */
		public function handle( Request $request, Closure $next ) {
			// Check the status of search API
			if ( $request->routeIs( 'search.*' ) ) {
				if ( TRUE !== \App\Models\Option::getOption( 'search', 'enable' ) ) {
					return response()->json(
						[
							'success' => FALSE,
							'message' => __( 'The search API has been disabled by the administrator.' )
						],
						401
					);
				}
			}

			return $next( $request );
		}
	}
