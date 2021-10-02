<?php

	namespace App\Exceptions;

	use Illuminate\Database\Eloquent\ModelNotFoundException;
	use Illuminate\Http\JsonResponse;

	class PostNotFoundException extends ModelNotFoundException {
		public function render( $request ): JsonResponse {
			return response()->json( [ 'success' => FALSE, 'error' => TRUE, 'message' => $this->getMessage() ], 404 );
		}
	}
