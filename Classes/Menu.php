<?php

	namespace WP_API;

	/**
	 * Class used to generate the menu items for
	 * the admin panel.
	 *
	 */
	class Menu {
		/**
		 * Hold an instance of the class.
		 *
		 * @var \WP_API\Menu $menu
		 */
		private static Menu $menu;

		/**
		 * Construct the menu items.
		 */
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'registerAdminMenus' ] );
		}

		/**
		 * initialize the menus.
		 */
		public static function init(): void {
			if ( ! isset( static::$menu ) ) {
				static::$menu = new Menu();
			}
		}

		/**
		 * Callback function to register the admin menus.
		 */
		public function registerAdminMenus(): void {
			// Top level menu
			add_menu_page(
				esc_html__( 'WP API', 'wp-api' ),
				esc_html__( 'WP API', 'wp-api' ),
				'manage_options',
				'wp-api-settings',
				'',
				'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMzY4IDM2OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzY4IDM2ODsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04OCwxMjguMDAxSDU2Yy0xMy4yMzIsMC0yNCwxMC43NjgtMjQsMjR2ODBjMCw0LjQxNiwzLjU3Niw4LDgsOHM4LTMuNTg0LDgtOHYtMjRoNDh2MjRjMCw0LjQxNiwzLjU3Niw4LDgsOHM4LTMuNTg0LDgtOA0KCQkJdi04MEMxMTIsMTM4Ljc2OSwxMDEuMjMyLDEyOC4wMDEsODgsMTI4LjAwMXogTTk2LDE5Mi4wMDFINDh2LTQwYzAtNC40MDgsMy41ODQtOCw4LThoMzJjNC40MTYsMCw4LDMuNTkyLDgsOFYxOTIuMDAxeiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNMTkyLDEyOC4wMDFoLTMyYy00LjQyNCwwLTgsMy41ODQtOCw4djk2YzAsNC40MTYsMy41NzYsOCw4LDhzOC0zLjU4NCw4LTh2LTI0aDI0YzIyLjA1NiwwLDQwLTE3Ljk0NCw0MC00MA0KCQkJQzIzMiwxNDUuOTQ1LDIxNC4wNTYsMTI4LjAwMSwxOTIsMTI4LjAwMXogTTE5MiwxOTIuMDAxaC0yNHYtNDhoMjRjMTMuMjMyLDAsMjQsMTAuNzY4LDI0LDI0UzIwNS4yMzIsMTkyLjAwMSwxOTIsMTkyLjAwMXoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTMyOCwyMjQuMDAxaC0yNHYtODBoMjRjNC40MjQsMCw4LTMuNTg0LDgtOHMtMy41NzYtOC04LThoLTY0Yy00LjQyNCwwLTgsMy41ODQtOCw4czMuNTc2LDgsOCw4aDI0djgwaC0yNA0KCQkJYy00LjQyNCwwLTgsMy41ODQtOCw4YzAsNC40MTYsMy41NzYsOCw4LDhoNjRjNC40MjQsMCw4LTMuNTg0LDgtOEMzMzYsMjI3LjU4NSwzMzIuNDI0LDIyNC4wMDEsMzI4LDIyNC4wMDF6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik0zNDQsNDguMDAxSDIxOS4zMTJsLTI5LjY1Ni0yOS42NTZjLTMuMTI4LTMuMTI4LTguMTg0LTMuMTI4LTExLjMxMiwwbC0yOS42NTYsMjkuNjU2SDI0Yy0xMy4yMzIsMC0yNCwxMC43NjgtMjQsMjR2MjQNCgkJCWMwLDQuNDE2LDMuNTc2LDgsOCw4czgtMy41ODQsOC04di0yNGMwLTQuNDA4LDMuNTg0LTgsOC04aDEyOGMyLjEyOCwwLDQuMTYtMC44NCw1LjY1Ni0yLjM0NEwxODQsMzUuMzEzbDI2LjM0NCwyNi4zNDQNCgkJCWMxLjQ5NiwxLjUwNCwzLjUyOCwyLjM0NCw1LjY1NiwyLjM0NGgxMjhjNC40MTYsMCw4LDMuNTkyLDgsOHYyNGMwLDQuNDE2LDMuNTc2LDgsOCw4czgtMy41ODQsOC04di0yNA0KCQkJQzM2OCw1OC43NjksMzU3LjIzMiw0OC4wMDEsMzQ0LDQ4LjAwMXoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTM2MCwyNjQuMDAxYy00LjQyNCwwLTgsMy41ODQtOCw4djI0YzAsNC40MDgtMy41ODQsOC04LDhIMjE2Yy0yLjEyOCwwLTQuMTYsMC44NC01LjY1NiwyLjM0NEwxODQsMzMyLjY4OQ0KCQkJbC0yNi4zNDQtMjYuMzQ0Yy0xLjQ5Ni0xLjUwNC0zLjUyOC0yLjM0NC01LjY1Ni0yLjM0NEgyNGMtNC40MTYsMC04LTMuNTkyLTgtOHYtMjRjMC00LjQxNi0zLjU3Ni04LTgtOHMtOCwzLjU4NC04LDh2MjQNCgkJCWMwLDEzLjIzMiwxMC43NjgsMjQsMjQsMjRoMTI0LjY4OGwyOS42NTYsMjkuNjU2YzEuNTYsMS41NiwzLjYwOCwyLjM0NCw1LjY1NiwyLjM0NGMyLjA0OCwwLDQuMDk2LTAuNzg0LDUuNjU2LTIuMzQ0DQoJCQlsMjkuNjU2LTI5LjY1NkgzNDRjMTMuMjMyLDAsMjQtMTAuNzY4LDI0LTI0di0yNEMzNjgsMjY3LjU4NSwzNjQuNDI0LDI2NC4wMDEsMzYwLDI2NC4wMDF6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPC9zdmc+DQo='
			);

			// Settings page
			add_submenu_page(
				'wp-api-settings',
				esc_html__( 'Settings', 'wp-api' ),
				esc_html__( 'Settings', 'wp-api' ),
				'manage_options',
				'wp-api-settings',
				[ Template::class, 'adminSetting' ],
			);

			// API page
			add_submenu_page(
				'wp-api-settings',
				esc_html__( 'Search', 'wp-api' ),
				esc_html__( 'Search', 'wp-api' ),
				'manage_options',
				'wp-api-search',
				[ Template::class, 'adminSearch' ]
			);
		}
	}