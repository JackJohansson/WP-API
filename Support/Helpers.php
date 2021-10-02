<?php

	/**
	 * Function to get an endpoint by its name.
	 */
	if ( ! function_exists( 'wpapi_get_endpoint' ) ) {
		function wpapi_get_endpoint( string $endpoint ): string {
			return \WP_API\Kernel::getEndpointUrl( $endpoint );
		}
	}