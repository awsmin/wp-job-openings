<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<div class="awsm-job-welcome">
		<div class="awsm-job-welcome-col">
			<div class="awsm-job-welcome-col-in awsm-job-welcome-l">
				<h1>
					<a heref="#" target="_blanl">
						<span></span>
						<?php esc_html_e( 'WP Job Openings', 'wp-job-openings' );?>
						<strong>by AWSM INNOVATIONS</strong>
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
				<form>
					<div class="awsm-job-form-group">
						<label><?php esc_html_e( 'Name of company', 'wp-job-openings' );?></label>
						<input type="text" class="awsm-job-form-control">
						<p><?php esc_html_e( 'The official name, which will be displayed all over', 'wp-job-openings' );?></p>
					</div><!-- .awsm-job-form-group -->
					<div class="awsm-job-form-group">
						<label><?php esc_html_e( 'Recruiter Email Address', 'wp-job-openings' );?></label>
						<input type="email" class="awsm-job-form-control">
						<p><?php esc_html_e( 'The email address that should be receiving all notifications', 'wp-job-openings' );?></p>
					</div><!-- .awsm-job-form-group -->
					<div class="awsm-job-form-group">
						<label><?php esc_html_e( 'Job listing page', 'wp-job-openings' );?></label>
						<select class="awsm-job-form-control"></select>
						<p><?php esc_html_e( 'The page you want to display the listing. You an choose it later also.', 'wp-job-openings' );?></p>
					</div><!-- .awsm-job-form-group -->
					<button class="button button-primary">Get Started</button>
				</form>
			</div><!-- .awsm-job-welcome-r -->
		</div><!-- .awsm-job-welcome-col -->
	</div>
