<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\HasMany;

	/**
	 * Model to interact with the users.
	 *
	 * @mixin \Illuminate\Database\Eloquent\Builder
	 */
	class User extends Model {

		/**
		 * Columns hidden from the search results.
		 *
		 * @var array $hidden
		 */
		protected $hidden = [
			'user_login',
			'user_pass',
			'user_email',
			'user_registered',
			'user_activation_key',
			'user_status'
		];

		/**
		 * Default name for the users table.
		 *
		 * @var string $table
		 */
		protected $table = 'users';

		/**
		 * Get the files uploaded by this user.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasMany
		 */
		public function attachments(): HasMany {
			return $this->hasMany( Attachment::class, 'post_author', 'ID' );
		}

		/**
		 * Return a custom table name, if set.
		 *
		 * @return string
		 */
		public function get_table(): string {
			if ( defined( 'WP_API_USERS_TABLE' ) ) {
				return WP_API_USERS_TABLE;
			}

			return $this->table;
		}

		/**
		 * Return the pages published by this user.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasMany
		 */
		public function pages(): HasMany {
			return $this->hasMany( Page::class, 'post_author', 'ID' );
		}

		/**
		 * Get the user's posts.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasMany
		 */
		public function posts(): HasMany {
			return $this->hasMany( Post::class, 'post_author', 'ID' );
		}
	}
