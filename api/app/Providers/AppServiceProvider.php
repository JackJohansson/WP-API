<?php

	namespace App\Providers;

	use App\Models\Option;
	use Illuminate\Support\ServiceProvider;

	class AppServiceProvider extends ServiceProvider {
		/**
		 * Bootstrap any application services.
		 *
		 * @return void
		 */
		public function boot() {
			// Load the options
			try {
				app()->make( Option::class )->optionsLoaded();
			} catch ( \Exception $exception ) {
				abort( 500, __( 'Could not load the system options. Please contact the website\'s administrator.' ) );
			}
		}

		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register() {
			$this->app->singleton( Option::class, function () { return new Option(); } );
		}

	}
