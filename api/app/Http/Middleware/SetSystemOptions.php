<?php

	namespace App\Http\Middleware;

	use App\Models\Option;
	use Closure;
	use Illuminate\Http\Request;

	class SetSystemOptions {
		/**
		 * Handle an incoming request.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param \Closure                 $next
		 *
		 * @return mixed
		 */
		public function handle( Request $request, Closure $next ) {
			try {
				if ( app()->make( Option::class )->optionsLoaded() ) {
					// Check if the API is enabled
					$enabled = Option::getOption( 'general', 'enable' );

					if ( ! $enabled ) {
						return response()->json(
							[
								'success' => FALSE,
								'error'   => FALSE,
								'message' => __( 'The API has been disabled. Please enabled via the plugin\'s setting.' )
							]
							,
							401
						);
					}

					return $next( $request );
				}

			} catch ( \Exception $exception ) {
				return response()->json(
					[
						'success' => FALSE,
						'error'   => TRUE,
						'message' => __( 'Could not load the system options. Please contact the website\'s administrator.' )
					],
					500
				);
			}

		}
	}
