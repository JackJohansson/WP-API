<?php

	namespace App\Exceptions;

	use Exception;
	use Illuminate\Http\JsonResponse;

	class InvalidKeywordException extends Exception {
		public function render( $request ): JsonResponse {
			return response()->json( [ 'success' => FALSE, 'error' => FALSE, 'message' => $this->getMessage() ], 422 );
		}
	}
