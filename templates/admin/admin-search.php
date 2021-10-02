<div class="content  grid__item grid__item--fluid grid grid--hor">
	<div class="container  container--fluid  grid__item grid__item--fluid">
		<div class="portlet portlet--mobile">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
				<div class="portlet__head">
					<div class="portlet__head-label">
						<h3 class="portlet__head-title">
							<?php esc_html_e( 'Search API Settings', 'wp-api' ); ?>
						</h3>
					</div>
				</div>
				<div class="portlet__body">

					<div class="section section--last">
						<!-- Enable Search API -->
						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="section__title section__title-sm"><?php esc_html_e( 'API Status', 'wp-api' ); ?></h3>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable API', 'wp-api' ); ?></label>
							<div class="col-lg-9 col-xl-6">
						<span class="switch">
							<label>
								<input type="checkbox" name="wp_api_enable_search" <?php checked( TRUE, \WP_API\Setting::getOption( 'search', 'enable' ) ) ?>>
								<span></span>
							</label>
						</span>
								<span class="form-text text-muted"><?php esc_html_e( 'Disable this if you want to disable the search API.', 'wp-api' ); ?></span>
							</div>
						</div>

						<!-- END Enable Search API -->
					</div>

					<div class="separator separator--dashed separator--lg"></div>

					<!-- Enable Search Cache-->
					<div class="section section--last">
						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="section__title section__title-sm"><?php esc_html_e( 'Cache Results?', 'wp-api' ); ?></h3>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable Cache', 'wp-api' ); ?></label>
							<div class="col-lg-9 col-xl-6">
								<span class="switch">
									<label>
										<input type="checkbox" name="wp_api_enable_search_cache" <?php checked( TRUE, \WP_API\Setting::getOption( 'search', 'cache' ) ) ?>>
										<span></span>
									</label>
								</span>
								<span class="form-text text-muted"><?php esc_html_e( 'Enable this option to cache the search results for even faster queries.', 'wp-api' ); ?></span>
							</div>
						</div>
					</div>
					<!-- END Enable Search Cache -->

					<div class="separator separator--dashed separator--lg"></div>

					<!-- Begin Auto Generate -->
					<div class="section section--last">
						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="section__title section__title-sm"><?php esc_html_e( 'Auto generate keywords', 'wp-api' ); ?></h3>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Auto Generate', 'wp-api' ); ?></label>
							<div class="col-lg-9 col-xl-6">
								<span class="switch">
									<label>
										<input type="checkbox" name="wp_api_enable_autogenerate" <?php checked( TRUE, \WP_API\Setting::getOption( 'search', 'autogenerate' ) ) ?>>
										<span></span>
									</label>
								</span>
								<span class="form-text text-muted"><?php esc_html_e( 'Enabling this option will automatically generate keywords based on the post\'s title.', 'wp-api' ); ?></span>
							</div>
						</div>
					</div>
					<!-- End Auto Generate -->

					<div class="separator separator--dashed separator--lg"></div>

					<!-- Begin Post Types -->
					<div class="section section--last">
						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="section__title section__title-sm"><?php esc_html_e( 'Post Types', 'wp-api' ); ?></h3>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enabled post types', 'wp-api' ); ?></label>
							<div class="col-lg-9 col-xl-6">
								<?php

									// Get registered custom post types
									$post_types = get_post_types(
										[
											'public'   => TRUE,
											'_builtin' => TRUE
										],
										'objects'
									);

									// A list of enabled custom post type
									$enabled_cpt = \WP_API\Setting::getOption( 'search', 'post_types', [] );

									if ( $post_types ) { ?>
										<div class="checkbox-list">
											<?php
												foreach ( $post_types as $post_type ) { ?>
													<label class="checkbox">
														<input
																type="checkbox"
																name="wp_api_search_post_types[]"
																value="<?php echo esc_attr( $post_type->name ); ?>"
															<?php checked( TRUE, in_array( $post_type->name, $enabled_cpt, TRUE ) ) ?>
														>
														<?php echo esc_html( $post_type->label ) ?>
														<span></span>
													</label>
													<?php
												}
											?>
										</div>
										<?php
									} else { ?>
										<div class="wp-api-vertical-align"><?php esc_html_e( 'No registered post types has been found.', 'wp-api' ); ?></div><?php
									}
								?>
								<span class="form-text text-muted mt-4"><?php esc_html_e( 'Select the post types that should be available via the API.', 'wp-api' ); ?></span>
							</div>
						</div>

					</div>
					<!-- End Post Types -->

				</div>
				<div class="portlet__foot">
					<div class="row">
						<div class="col-lg-12 align-right">
							<button class="btn btn-primary" type="submit">
								<i class="la la-save"></i><?php esc_html_e( 'Save', 'wp-api' ); ?></button>
						</div>
					</div>
				</div>
				<input type="hidden" name="action" value="wp_api_save_search_options">
				<?php wp_nonce_field( 'wp-api-save-search-options', 'wp_api_search_options_nonce' ) ?>
			</form>
		</div>
	</div>
</div>