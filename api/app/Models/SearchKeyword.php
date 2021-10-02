<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsToMany;
	use Illuminate\Database\Query\JoinClause;

	/**
	 * Model used to interact with the search keywords.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class SearchKeyword extends Model {
		/**
		 * Name of the keywords table.
		 *
		 * @var string $table
		 */
		protected $table = 'wpapi_search_keywords';

		/**
		 * Get all the posts related to this keyword.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function allPosts(): BelongsToMany {
			return $this->belongsToMany( GeneralPost::class, $this->pivotName(), 'keyword_id', 'post_id', 'id', 'ID' );
		}

		/**
		 * Try to resolve custom pivot table name.
		 *
		 * @return string
		 */
		private function pivotName(): string {
			return defined( 'WP_API_KEYWORDS_PIVOT_TABLE' ) ? WP_API_KEYWORDS_PIVOT_TABLE : 'wpapi_search_keywords_pivot';
		}

		/**
		 * Retrieve all the attachments that match this keyword.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function attachments(): BelongsToMany {
			return $this->belongsToMany( Attachment::class, $this->pivotName(), 'keyword_id', 'post_id', 'id', 'ID' );
		}

		/**
		 * Get all the custom post types matching this keyword.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function customPostTypes(): BelongsToMany {
			return $this->belongsToMany( CustomPostType::class, $this->pivotName(), 'keyword_id', 'post_id', 'id', 'ID' )
			            ->select( [ 'posts.*', 'thumbnail.meta_value as thumbnails' ] )
			            ->distinct()
			            ->leftJoin( 'postmeta as attachment', 'posts.ID', '=', 'attachment.post_id' )
			            ->leftJoin(
				            'postmeta as thumbnail',
				            function ( JoinClause $join ) {
					            $join->on( 'attachment.meta_value', '=', 'thumbnail.post_id' );
					            $join->where( 'thumbnail.meta_key', '=', '_wp_attachment_metadata' );
					            $join->where( 'attachment.meta_key', '=', '_thumbnail_id' );
				            }
			            )
			            ->orderBy( 'thumbnails', 'desc' );
		}

		/**
		 * Return a custom table name, if set.
		 *
		 * @return string
		 */
		public function get_table(): string {
			if ( defined( 'WP_API_KEYWORDS_TABLE' ) ) {
				return WP_API_KEYWORDS_TABLE;
			}

			return $this->table;
		}

		/**
		 * A list of words to be excluded from the search.
		 *
		 * @return string[]
		 */
		public static function listPropositions(): array {
			return [
				'at',
				'but',
				'by',
				'for',
				'on',
				'in',
				'off',
				'of',
				'up',
				'the',
				'to',
				'the',
				'thus',
				'though',
			];
		}

		/**
		 * Retrieve all the posts that match this keyword.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function pages(): BelongsToMany {
			return $this->belongsToMany( Page::class, $this->pivotName(), 'keyword_id', 'post_id', 'id', 'ID' )
			            ->select( [ 'posts.*', 'thumbnail.meta_value as thumbnails' ] )
			            ->distinct()
			            ->leftJoin( 'postmeta as attachment', 'posts.ID', '=', 'attachment.post_id' )
			            ->leftJoin(
				            'postmeta as thumbnail',
				            function ( JoinClause $join ) {
					            $join->on( 'attachment.meta_value', '=', 'thumbnail.post_id' );
					            $join->where( 'thumbnail.meta_key', '=', '_wp_attachment_metadata' );
					            $join->where( 'attachment.meta_key', '=', '_thumbnail_id' );
				            }
			            )
			            ->orderBy( 'thumbnails', 'desc' );
		}

		/**
		 * Retrieve all the posts that match this keyword.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function posts(): BelongsToMany {
			return $this->belongsToMany( Post::class, $this->pivotName(), 'keyword_id', 'post_id', 'id', 'ID' )
			            ->select( [ 'posts.*', 'thumbnail.meta_value as thumbnails' ] )
			            ->distinct()
			            ->leftJoin( 'postmeta as attachment', 'posts.ID', '=', 'attachment.post_id' )
			            ->leftJoin(
				            'postmeta as thumbnail',
				            function ( JoinClause $join ) {
					            $join->on( 'attachment.meta_value', '=', 'thumbnail.post_id' );
					            $join->where( 'thumbnail.meta_key', '=', '_wp_attachment_metadata' );
					            $join->where( 'attachment.meta_key', '=', '_thumbnail_id' );
				            }
			            )
			            ->orderBy( 'thumbnails', 'desc' );
		}
	}
