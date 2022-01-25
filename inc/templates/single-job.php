<?php
/**
 * Template for displaying single job listing
 *
 * This template can be overridden by copying it to currenttheme/wp-job-openings/single-job.php
 *
 * @package wp-job-openings
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

awsm_jobs_get_header();

/**
 * before_awsm_jobs_main_content hook
 *
 * @since 1.1
 */
do_action( 'before_awsm_jobs_main_content' );

?>
	<div class="awsm-job-main awsm-job-single-content">
		<div class="awsm-job-container">
			<?php
				/**
				 * before_awsm_jobs_single_loop hook
				 *
				 * Fires before The Loop to query for single job listing
				 *
				 * @since 1.1
				 */
				do_action( 'before_awsm_jobs_single_loop' );

			while ( have_posts() ) {
				the_post();

				/**
				 * before_awsm_jobs_single_content hook
				 *
				 * @hooked awsm_jobs_single_title
				 * @hooked awsm_jobs_single_featured_image
				 *
				 * @since 1.1
				 */
				do_action( 'before_awsm_jobs_single_content' );

				the_content();

				/**
				 * after_awsm_jobs_single_content hook
				 *
				 * @since 1.1
				 */
				do_action( 'after_awsm_jobs_single_content' );
			}

				/**
				 * after_awsm_jobs_single_loop hook
				 *
				 * Fires after The Loop
				 *
				 * @since 1.1
				 */
				do_action( 'after_awsm_jobs_single_loop' );
			?>
		</div>
	</div>
<?php
/**
 * after_awsm_jobs_main_content hook
 *
 * @since 1.1
 */
do_action( 'after_awsm_jobs_main_content' );

awsm_jobs_get_footer();
