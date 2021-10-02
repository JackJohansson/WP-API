<div class="content grid__item grid__item--fluid grid grid--hor">
	<div class="container container--fluid grid__item grid__item--fluid">
		<div class="portlet portlet--mobile">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
				<div class="portlet__head">
					<div class="portlet__head-label">
						<h3 class="portlet__head-title">
							<?php esc_html_e( 'General Settings', 'wp-api' ); ?>
						</h3>
					</div>
				</div>
				<div class="portlet__body">

					<div class="section section--last">
						<!-- Enable API -->
						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="section__title section__title-sm"><?php esc_html_e( 'API Status', 'wp-api' ); ?></h3>
							</div>
						</div>

						<div class="form-group form-group-last row">
							<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable API', 'wp-api' ); ?></label>
							<div class="col-lg-9 col-xl-6">
						<span class="switch">
							<label>
								<input type="checkbox" name="wp_api_enable" <?php checked( TRUE, \WP_API\Setting::getOption( 'general', 'enable' ) ) ?>>
								<span></span>
							</label>
						</span>
								<span class="form-text text-muted"><?php esc_html_e( 'Disable this if you want to disable the entire API.', 'wp-api' ); ?></span>
							</div>
						</div>

						<!-- END Enable API -->
					</div>

					<div class="separator separator--dashed separator--lg"></div>

					<div class="section section--last">
						<!-- Throttle API -->
						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="section__title section__title-sm"><?php esc_html_e( 'Rate Limit', 'wp-api' ); ?></h3>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Throttle API?', 'wp-api' ); ?></label>
							<div class="col-lg-9 col-xl-6">
								<span class="switch">
									<label>
										<input type="checkbox" name="wp_api_throttle" <?php checked( TRUE, \WP_API\Setting::getOption( 'general', 'throttle' ) ) ?>>
										<span></span>
									</label>
								</span>
								<span class="form-text text-muted"><?php esc_html_e( 'Enabling this will limit the rate of requests that can be make to an endpoint.', 'wp-api' ); ?></span>
							</div>
						</div>

						<div class="form-group form-group-last row">
							<label class="col-xl-3 col-lg-3"></label>
							<div class="col-lg-3 wp-api-vertical-align"><?php esc_html_e( 'Limit the attempts to a maximum of', 'wp-api' ); ?></div>
							<div class="col-lg-2">
								<input
										type="text"
										class="form-control text-center wp-api-rate-per"
										value="<?php echo esc_attr( \WP_API\Setting::getOption( 'general', 'rate_count', 60 ) ) ?>"
										name="wp_api_rate"
										placeholder="<?php esc_html_e( 'Enter a value', 'wp-api' ); ?>"
								>
							</div>
							<div class="col-lg-2 wp-api-vertical-align text-center"><?php esc_html_e( 'requests per every', 'wp-api' ); ?></div>
							<div class="col-lg-2">
								<select class="select2 wp-api-select2" name="wp_api_rate_unit">
									<option <?php echo 'min' === \WP_API\Setting::getOption( 'general', 'rate_unit' ) ? 'selected' : '' ?> value="min"><?php esc_html_e( 'Minute', 'wp-api' ); ?></option>
									<option <?php echo 'hour' === \WP_API\Setting::getOption( 'general', 'rate_unit' ) ? 'selected' : '' ?> value="hour"><?php esc_html_e( 'Hour', 'wp-api' ); ?></option>
									<option <?php echo 'day' === \WP_API\Setting::getOption( 'general', 'rate_unit' ) ? 'selected' : '' ?> value="day"><?php esc_html_e( 'Day', 'wp-api' ); ?></option>
								</select>
							</div>
						</div>
						<!-- End Throttle API -->

						<div class="separator separator--dashed separator--lg"></div>

						<div class="section section--last">
							<!-- Token -->
							<div class="row">
								<label class="col-xl-3"></label>
								<div class="col-lg-9 col-xl-6">
									<h3 class="section__title section__title-sm"><?php esc_html_e( 'API Token', 'wp-api' ); ?></h3>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label"><?php esc_html_e( 'Generated Token', 'wp-api' ); ?></label>
								<div class="col-lg-9">
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="la la-lock"></i></span>
										</div>
										<input type="text" class="form-control" disabled="disabled" value="<?php echo esc_attr( get_option( 'wp-api-token', 'N/A' ) ) ?>">
										<div class="input-group-append">
											<button
													class="btn btn-success"
													type="button"
													id="wp-api-regenerate-token"
													data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp-api-generate-token' ) ) ?>"
													data-url="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>"
											>
												<i class="la la-refresh font-light"></i> <?php esc_html_e( 'Regenerate', 'wp-api' ); ?>
											</button>
										</div>
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'This token will be used internally to perform system actions. You can regenerate this token if you need to do so for any reason.', 'wp-api' ); ?></span>
								</div>
							</div>

						</div>

					</div>


				</div>
				<div class="portlet__foot">
					<div class="row">
						<div class="col-lg-12 align-right">
							<button class="btn btn-primary" type="submit">
								<i class="la la-save"></i><?php esc_html_e( 'Save', 'wp-api' ); ?></button>
						</div>
					</div>
				</div>
				<input type="hidden" name="action" value="wp_api_save_general_options">
				<?php wp_nonce_field( 'wp-api-save-general-options', 'wp_api_general_options_nonce' ) ?>
			</form>
		</div>
	</div>
</div>