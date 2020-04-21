<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$default_listing_page_id  = get_option( 'awsm_jobs_default_listing_page_id' );
	$selected_listing_page_id = get_option( 'awsm_select_page_listing', $default_listing_page_id );
	$selected_page_status     = get_post_status( $selected_listing_page_id );
	$page_exists              = ( $selected_page_status === 'publish' ) ? true : false;
	$args                     = array(
		'id'       => 'awsm-jobs-company-listing-page-field',
		'name'     => 'awsm_jobs_listing_setup[awsm_select_page_listing]',
		'class'    => 'awsm-job-form-control',
		'selected' => $selected_listing_page_id,
	);
	if ( ! $page_exists ) {
		$args['selected']         = '';
		$args['show_option_none'] = esc_html__( 'Select a page', 'wp-job-openings' );
	}
?>
	<div class="awsm-job-welcome">
		<div class="awsm-job-welcome-col">
			<div class="awsm-job-welcome-col-in awsm-job-welcome-l">
				<h1>
					<a heref="#" target="_blanl">
						<span></span>
						<?php esc_html_e( 'WP Job Openings', 'wp-job-openings' );?>
						<strong><?php esc_html_e( 'by AWSM INNOVATIONS', 'wp-job-openings' );?></strong>
					</a>
				</h1>
				<p><?php esc_html_e( 'Thank you for trying WP Job Openings Plugin by AWSM Innovations. The plugin will help you setup the jobs page for in a few minutes.', 'wp-job-openings' );?></p>
				<ul class="awsm-job-welcome-step-list">
					<li><?php esc_html_e( 'Set up the listing page', 'wp-job-openings' );?></li>
					<li><?php esc_html_e( 'Add job openings', 'wp-job-openings' );?></li>
					<li><?php esc_html_e( 'Start hunting talents!', 'wp-job-openings' );?></li>
				</ul>
			</div><!-- .awsm-job-welcome-l -->
		</div><!-- .awsm-job-welcome-col -->
		<div class="awsm-job-welcome-col">
			<div class="awsm-job-welcome-col-in awsm-job-welcome-r">
				<h2><?php esc_html_e( "Let's set up your job listing", "wp-job-openings" );?></h2>
				<form  action="#" class="awsm-job-welcome-page-form">
					<div class="awsm-job-form-group">
						<label><?php esc_html_e( 'Name of company', 'wp-job-openings' );?></label>
						<input type="text" name="awsm_jobs_listing_setup[awsm_job_company_name]" class="awsm-job-form-control" id="awsm-jobs-company-name-field" required />
						<p><?php esc_html_e( 'The official name, which will be displayed all over', 'wp-job-openings' );?></p>
					</div><!-- .awsm-job-form-group -->
					<div class="awsm-job-form-group">
						<label><?php esc_html_e( 'Recruiter Email Address', 'wp-job-openings' );?></label>
						<input type="email" name="awsm_jobs_listing_setup[awsm_hr_email_address]" class="awsm-job-form-control" id="awsm-jobs-company-email-field" required />
						<p><?php esc_html_e( 'The email address that should be receiving all notifications', 'wp-job-openings' );?></p>
					</div><!-- .awsm-job-form-group -->
					<div class="awsm-job-form-group">
						<label><?php esc_html_e( 'Job listing page', 'wp-job-openings' );?></label>
						  <?php wp_dropdown_pages( $args ); ?>
						<p><?php esc_html_e( 'The page you want to display the listing. You an choose it later also.', 'wp-job-openings' );?></p>
					</div><!-- .awsm-job-form-group -->
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>" class="button button-primary button-large awsm-jobs-get-started" id="awsm-jobs-get-started" data-nonce="<?php echo wp_create_nonce( 'awsm-job-setup-page-nonce' ); ?>" disabled><?php esc_html_e( 'Get Started', 'wp-job-openings' ); ?></a>
				</form>
			</div><!-- .awsm-job-welcome-r -->
		</div><!-- .awsm-job-welcome-col -->
	</div>
