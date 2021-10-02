<?php

	namespace App\Exceptions;

	use Exception;

	/**
	 * Exception to handle disabled API endpoints.
	 */
	class ApiDisabledException extends Exception {
		public function render() {
			return response()->json( [ 'success' => FALSE, 'message' => $this->getMessage() ], 403 );
		}
	}
