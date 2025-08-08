<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$default_page_id  = get_option( 'awsm_jobs_default_listing_page_id' );
	$selected_page_id = get_option( 'awsm_select_page_listing', $default_page_id );
	$args             = array(
		'name'     => 'awsm_select_page_listing',
		'class'    => 'awsm-job-form-control',
		'selected' => $selected_page_id,
	);
	$company_name     = get_option( 'awsm_job_company_name' );
	?>
	<div class="awsm-job-setup">
		<div class="awsm-job-setup-in">
			<div class="awsm-job-setup-head">
				<h1>
				<a href="https://wpjobopenings.com" target="_blank">
						<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/hirezoot-logo-b.svg' ); ?>" align="<?php esc_html_e( 'HireZoot by AWSM INNOVATIONS', 'wp-job-openings' ); ?>">
						<span><?php esc_html_e( 'HireZoot by AWSM INNOVATIONS', 'wp-job-openings' ); ?></span>
					</a>
				</h1>
				<p>
					<span><?php esc_html_e( 'Thanks for installing Hirezoot, you are awesome! ', 'wp-job-openings' ); ?></span>
					<span><?php esc_html_e( 'Your are a few minutes away from setting up your job page and start hiring.', 'wp-job-openings' ); ?></span>
				</p>
			</div><!-- .awsm-job-setup-head-->
			<div class="awsm-job-setup-form">
				<div class="awsm-job-setup-form-in">
					<div class="awsm-job-setup-form-head">
						<h2><?php esc_html_e( 'Let’s get started 🚀', 'wp-job-openings' ); ?></h2>
						<ul>
							<li class="active"><?php esc_html_e( '1', 'wp-job-openings' ); ?></li>
							<li><?php esc_html_e( '2', 'wp-job-openings' ); ?></li>
							<li><?php esc_html_e( '3', 'wp-job-openings' ); ?></li>
						</ul>
					</div><!-- .awsm-job-setup-form-head -->
					<form method="POST" action="" id="awsm-job-setup-form">
						<div class="awsm-job-setup-form-main">
							<div class="awsm-job-setup-form-main-in">
								<div class="awsm-job-setup-form-item active">
									<label for="awsm_job_company_name"><?php esc_html_e( 'Name of company', 'wp-job-openings' ); ?></label>
									<input type="text" name="awsm_job_company_name" class="awsm-job-form-control" id="awsm_job_company_name" value="<?php echo esc_attr( $company_name ); ?>" required />
									<p><?php esc_html_e( 'Please enter the full name of your hiring company', 'wp-job-openings' ); ?></p>
								</div><!-- .awsm-job-setup-form-item -->
								<div class="awsm-job-setup-form-item">
									<label for="awsm_hr_email_address"><?php esc_html_e( 'Recruiter Email Address', 'wp-job-openings' ); ?></label>
									<input type="email" name="awsm_hr_email_address" class="awsm-job-form-control" id="awsm_hr_email_address" value="<?php echo esc_attr( get_option( 'awsm_hr_email_address' ) ); ?>" required />
									<p><?php esc_html_e( 'Enter the email you want to receive applications and notifications', 'wp-job-openings' ); ?></p>
								</div><!-- .awsm-job-setup-form-item -->
								<div class="awsm-job-setup-form-item">
									<label for="awsm_select_page_listing"><?php esc_html_e( 'Job listing page', 'wp-job-openings' ); ?></label>
									<?php
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										wp_dropdown_pages( $args );
									?>
									<p><?php esc_html_e( 'Select the page where your job listings will be displayed', 'wp-job-openings' ); ?></p>
								</div><!-- .awsm-job-setup-form-item -->
							</div><!-- .awsm-job-setup-form-main-in -->
						</div><!-- .awsm-job-setup-form-main -->
						<div class="awsm-job-setup-form-foot">
							<div class="awsm-job-setup-form-foot-top">
								<a href="#" class="awsm-job-setup-button awsm-job-setup-button-next active"><?php esc_html_e( 'Next', 'wp-job-openings' ); ?></a>
								<input type="hidden" name="action" value="awsm_jobs_setup" />
								<?php wp_nonce_field( 'awsm-jobs-setup', 'awsm_job_nonce' ); ?>
								<!-- <button class="awsm-job-setup-button" id="awsm-jobs-setup-btn"><?php esc_html_e( 'Get Started', 'wp-job-openings' ); ?></button> -->
								<input type="submit" class="awsm-job-setup-button" id="awsm-jobs-setup-btn" value="<?php esc_html_e( 'Get Started', 'wp-job-openings' ); ?>" />
							</div><!-- .awsm-job-setup-form-foot-top -->
							<div class="awsm-job-setup-form-foot-bottom">
								<a href="#" class="awsm-job-setup-back"><?php esc_html_e( '← Back', 'wp-job-openings' ); ?></a>
							</div><!-- .awsm-job-setup-form-foot-bottom -->
						</div><!-- .awsm-job-setup-form-foot -->
					</form>
				</div><!-- .awsm-job-setup-form-in -->
			</div><!-- .awsm-job-setup-form -->
			<div class="awsm-job-setup-foot">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-overview' ) ); ?>"><?php esc_html_e( 'Skip to dashboard', 'wp-job-openings' ); ?></a>
			</div>
		</div><!-- .awsm-job-setup-in -->
	</div><!-- .awsm-job-setup -->
