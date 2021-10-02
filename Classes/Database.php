<?php

	namespace WP_API;

	/**
	 * Class used to perform actions
	 * on the database.
	 *
	 */
	class Database {

		public const DB_PREFIX            = 'wpapi_';
		public const KEYWORDS_TABLE       = 'search_keywords';
		public const KEYWORDS_PIVOT_TABLE = 'search_keywords_pivot';

		/**
		 * Create the required tables.
		 */
		public static function createTables(): void {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// Get the tables names
			$prefix              = defined( 'WP_API_DB_PREFIX' ) ? WP_API_DB_PREFIX : self::DB_PREFIX;
			$keyword_pivots_name = defined( 'WP_API_KEYWORDS_PIVOT_TABLE' ) ? WP_API_KEYWORDS_PIVOT_TABLE : self::KEYWORDS_PIVOT_TABLE;
			$keyword_name        = defined( 'WP_API_KEYWORDS_TABLE' ) ? WP_API_KEYWORDS_TABLE : self::KEYWORDS_TABLE;

			// Keywords table
			$keywords = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}{$prefix}{$keyword_name}`
				(
				  	`id` 		BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				  	`value` 	VARCHAR(255) NOT NULL,
				  	PRIMARY KEY ( `id` ),
                	INDEX ( `value` ),
					CONSTRAINT `unique_keyword` UNIQUE ( `value` ) 
				) 
    			$charset_collate;";

			// Keyword Pivots table
			$keyword_pivots = "CREATE TABLE if NOT EXISTS `{$wpdb->prefix}{$prefix}{$keyword_pivots_name}`
				(
				    `id`			BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				    `keyword_id` 	BIGINT UNSIGNED NOT NULL,
				    `post_id`		BIGINT UNSIGNED NOT NULL,
				    PRIMARY KEY ( `id` ),
    				INDEX ( `keyword_id`, `post_id` ),
				    CONSTRAINT `post_id_fk` FOREIGN KEY ( `post_id` ) REFERENCES `$wpdb->posts` ( `ID` ) ON DELETE CASCADE ON UPDATE CASCADE,
				    CONSTRAINT `keyword_fk` FOREIGN KEY ( `keyword_id` ) REFERENCES `{$wpdb->prefix}{$prefix}{$keyword_name}` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE
				)
				$charset_collate";

			dbDelta( $keywords );
			dbDelta( $keyword_pivots );
		}

		/**
		 * Remove the tables.
		 */
		public static function removeTables(): void {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// Get the tables names
			$prefix              = defined( 'WP_API_DB_PREFIX' ) ? WP_API_DB_PREFIX : self::DB_PREFIX;
			$keyword_pivots_name = defined( 'WP_API_KEYWORDS_PIVOT_TABLE' ) ? WP_API_KEYWORDS_PIVOT_TABLE : self::KEYWORDS_PIVOT_TABLE;
			$keyword_name        = defined( 'WP_API_KEYWORDS_TABLE' ) ? WP_API_KEYWORDS_TABLE : self::KEYWORDS_TABLE;

			// Drop foreign keys
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}{$prefix}{$keyword_pivots_name}` DROP FOREIGN KEY `post_id_fk`;" );
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}{$prefix}{$keyword_pivots_name}` DROP FOREIGN KEY `keyword_fk`;" );

			// Delete the tables
			$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}{$prefix}{$keyword_pivots_name}`;" );
			$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}{$prefix}{$keyword_name}`;" );
		}

		private static function setConstraints(): void { }
	}