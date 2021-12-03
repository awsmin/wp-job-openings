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
		<div class="awsm-job-setup-col">
			<div class="awsm-job-setup-col-in awsm-job-setup-l">
				<h1>
					<a href="https://wpjobopenings.com" target="_blank">
						<span></span>
						<?php esc_html_e( 'WP Job Openings', 'wp-job-openings' ); ?>
						<strong><?php esc_html_e( 'by AWSM INNOVATIONS', 'wp-job-openings' ); ?></strong>
					</a>
				</h1>
				<p>
				<?php
					esc_html_e( 'Thanks for installing WP Job Openings, you are awesome! With WP Job Openings, it will take you only a few minutes to set up your job listing page and start hiring.', 'wp-job-openings' );
				?>
				</p>
				<ul class="awsm-job-setup-step-list">
					<li><?php esc_html_e( 'Set up the listing page', 'wp-job-openings' ); ?></li>
					<li><?php esc_html_e( 'Add job openings', 'wp-job-openings' ); ?></li>
					<li><?php esc_html_e( 'Start hunting talents!', 'wp-job-openings' ); ?></li>
				</ul>
			</div><!-- .awsm-job-setup-l -->
		</div><!-- .awsm-job-setup-col -->
		<div class="awsm-job-setup-col">
			<div class="awsm-job-setup-col-in awsm-job-setup-r">
				<h2><?php esc_html_e( "Let's set up your job listing", 'wp-job-openings' ); ?></h2>
				<div class="awsm-job-setup-notice notice notice-error awsm-hide"></div>
				<form method="POST" action="" id="awsm-job-setup-form">
					<div class="awsm-job-form-group">
						<label for="awsm_job_company_name"><?php esc_html_e( 'Name of company', 'wp-job-openings' ); ?></label>
						<input type="text" name="awsm_job_company_name" class="awsm-job-form-control" id="awsm_job_company_name" value="<?php echo esc_attr( $company_name ); ?>" required />
						<p><?php esc_html_e( 'The official name, which will be displayed all over', 'wp-job-openings' ); ?></p>
					</div><!-- .awsm-job-form-group -->
					<div class="awsm-job-form-group">
						<label for="awsm_hr_email_address"><?php esc_html_e( 'Recruiter Email Address', 'wp-job-openings' ); ?></label>
						<input type="email" name="awsm_hr_email_address" class="awsm-job-form-control" id="awsm_hr_email_address" value="<?php echo esc_attr( get_option( 'awsm_hr_email_address' ) ); ?>" required />
						<p><?php esc_html_e( 'The email address that should be receiving all notifications', 'wp-job-openings' ); ?></p>
					</div><!-- .awsm-job-form-group -->
					<div class="awsm-job-form-group">
						<label for="awsm_select_page_listing"><?php esc_html_e( 'Job listing page', 'wp-job-openings' ); ?></label>
						<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							wp_dropdown_pages( $args );
						?>
						<p><?php esc_html_e( 'The page you want to display the listing. You an choose it later also.', 'wp-job-openings' ); ?></p>
					</div><!-- .awsm-job-form-group -->
					<input type="hidden" name="action" value="awsm_jobs_setup" />
					<?php wp_nonce_field( 'awsm-jobs-setup', 'awsm_job_nonce' ); ?>
					<input type="submit" class="button button-primary" id="awsm-jobs-setup-btn" value="<?php esc_html_e( 'Get Started', 'wp-job-openings' ); ?>" />
				</form>
			</div><!-- .awsm-job-setup-r -->
		</div><!-- .awsm-job-setup-col -->
	</div>
