<?php

	namespace App\Http\Controllers;

	use App\Exceptions\ApiDisabledException;
	use App\Exceptions\InvalidKeywordException;
	use App\Exceptions\PostNotFoundException;
	use App\Models\Option;
	use App\Models\SearchKeyword;
	use App\Support\Cache;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Relations\Relation;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Support\Collection;
	use Illuminate\Support\Str;

	/**
	 * Class used to interact with the post models.
	 *
	 */
	class PostController extends Controller {

		/**
		 * Perform a search on attachments.
		 *
		 * @param string $keyword
		 *
		 * @return \Illuminate\Http\JsonResponse
		 * @throws \Exception
		 */
		public function searchAttachmentsPublic( string $keyword ): JsonResponse {
			// Try to find a matching item
			return response()->json( $this->searchGeneralPosts( 'attachments', $keyword ) );
		}

		/**
		 * Perform a search for a specific keyword.
		 *
		 * @param string $post_type
		 * @param string $keyword
		 *
		 * @return array
		 * @throws \Exception
		 */
		public function searchGeneralPosts( string $post_type, string $keyword ): array {

			// Check API status
			$enabled_post_types = Option::getOption( 'search', 'post_types' );

			// If this API is not enabled
			if ( ! in_array( Str::singular( $post_type ), $enabled_post_types, TRUE ) ) {
				throw new ApiDisabledException( __( 'This API endpoint has been disabled by the administrator.' ) );
			}

			// Generate the keywords
			$keywords = $this->parseKeyword( $keyword );

			// Cache results if enabled
			if ( FALSE !== Option::getOption( 'search', 'cache' ) ) {
				$posts = Cache::getOrSet(
					"{$post_type}_search_cache",
					md5( $keyword ),
					function () use ( $keywords, $post_type ) {
						return $this->getResults( $post_type, $keywords );
					}
				);
			} else {
				// Query all the posts that match this.
				$posts = $this->getResults( $post_type, $keywords );
			}

			// No results
			if ( NULL === $posts || 0 === $posts->count() ) {
				throw new PostNotFoundException( __( 'There are no records matching your search criteria.' ) );
			}

			return $posts->toArray();
		}

		/**
		 * Parse the keyword and return an array of keywords.
		 *
		 * @param string $keyword
		 *
		 * @return array
		 * @throws \App\Exceptions\InvalidKeywordException
		 */
		private function parseKeyword( string $keyword ): array {

			// Generate an array of keywords from the search string
			$keywords = collect( explode( ' ', trim( $keyword ) ) );

			// Only return the items that are at least 3 characters long
			$keywords->map(
				function ( $item ) {
					return strlen( $item ) > 2 ? $item : FALSE;
				}
			// Remove the propositions from the array
			)->diff( SearchKeyword::listPropositions() );

			// Keyword is too short.
			if ( 0 === $keywords->count() ) {
				throw new InvalidKeywordException( __( 'Nothing has matched your keyword, please try a stronger phrase.' ) );
			}

			return $keywords->toArray();
		}

		/**
		 * Query the database and return the results.
		 *
		 * @param string $post_type
		 * @param array  $keywords
		 *
		 * @return \Illuminate\Support\Collection
		 */
		private function getResults( string $post_type, array $keywords ): Collection {
			return SearchKeyword::query()
			                    ->whereIn( 'value', $keywords )
			                    ->whereHas( $post_type )
			                    ->with( $post_type )
			                    ->get()
			                    ->pluck( $post_type )
			                    ->flatten()
			                    ->unique( 'ID' );
		}

		/**
		 * Perform a keyword search on a custom post type.
		 *
		 * @param string $cpt
		 * @param string $keyword
		 *
		 * @return \Illuminate\Http\JsonResponse
		 * @throws \App\Exceptions\InvalidKeywordException
		 */
		public function searchCptPublic( string $cpt, string $keyword ): JsonResponse {
			// Try to find a matching item
			return response()->json( $this->searchCustomPostTypes( $cpt, $keyword ) );
		}

		/**
		 * Perform a keyword search on a custom post type.
		 *
		 * @param string $cpt
		 * @param string $keyword
		 *
		 * @return array
		 * @throws \App\Exceptions\InvalidKeywordException
		 */
		private function searchCustomPostTypes( string $cpt, string $keyword ): array {

			// Generate the keywords
			$keywords = $this->parseKeyword( $keyword );

			// Cache results if enabled
			if ( FALSE !== Option::getOption( 'search', 'cache' ) ) {
				$result = Cache::getOrSet(
					"{$cpt}_search_cache",
					md5( $keyword ),
					function () use ( $keywords, $cpt ) {
						return $this->getResultsCpt( $cpt, $keywords );
					}
				);
			} else {
				// Query all the posts that match this.
				$result = $this->getResultsCpt( $cpt, $keywords );
			}

			// No results
			if ( NULL === $result || 0 === $result->count() ) {
				throw new PostNotFoundException( __( 'There are no records matching your search criteria.' ) );
			}

			return $result->toArray();
		}

		/**
		 * Query custom post types and return the results.
		 *
		 * @param string $cpt
		 * @param array  $keywords
		 *
		 * @return \Illuminate\Support\Collection
		 */
		private function getResultsCpt( string $cpt, array $keywords ): Collection {
			return SearchKeyword::query()
			                    ->whereIn( 'value', $keywords )
			                    ->whereHas( 'customPostTypes', function ( Builder $builder ) use ( $cpt ) { $builder->where( 'post_type', '=', $cpt ); } )
			                    ->with( [ 'customPostTypes' => function ( Relation $relation ) use ( $cpt ) { $relation->where( 'post_type', '=', $cpt ); } ] )
			                    ->get()
			                    ->pluck( 'customPostTypes' )
			                    ->flatten()
			                    ->unique( 'ID' );
		}

		/**
		 * Perform a search on the page model.
		 *
		 * @param string $keyword
		 *
		 * @return \Illuminate\Http\JsonResponse
		 * @throws \Exception
		 */
		public function searchPagesPublic( string $keyword ): JsonResponse {
			// Try to find a matching item
			return response()->json( $this->searchGeneralPosts( 'pages', $keyword ) );
		}

		/**
		 * Perform a search on the post model.
		 *
		 * @param string $keyword
		 *
		 * @return \Illuminate\Http\JsonResponse
		 * @throws \Exception
		 */
		public function searchPostsPublic( string $keyword ): JsonResponse {
			// Try to find a matching item
			return response()->json( $this->searchGeneralPosts( 'posts', $keyword ) );
		}
	}
