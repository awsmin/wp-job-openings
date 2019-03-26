<?php
/**
 * Template for displaying job openings (used by shortcode too)
 *
 * Override this by copying it to currenttheme/wp-job-openings/job-openings-view.php
 *
 * @package wp-job-openings
 * @version 1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode_atts = isset( $shortcode_atts ) ? $shortcode_atts : array();

/**
 * before_awsm_jobs_listing hook
 *
 * @hooked awsm_jobs_archive_title
 *
 * @since 1.1
 */
do_action( 'before_awsm_jobs_listing' );

$query = awsm_jobs_query( $shortcode_atts );

if ( $query->have_posts() ) : ?>
	<div class="awsm-job-wrap">

		<?php
			/**
			 * awsm_filter_form hook
			 *
			 * Display filter form for job listings
			 *
			 * @hooked AWSM_Job_Openings_Filters::display_filter_form()
			 *
			 * @since 1.0
			 */
			do_action( 'awsm_filter_form', $shortcode_atts );
		?>

		<div 
		<?php
		awsm_jobs_view_class();
		awsm_jobs_data_attrs( array(), $shortcode_atts );
		?>
		>
			<?php include get_awsm_jobs_template_path( 'main', 'job-openings' ); ?>
		</div>

	</div>
	<?php
else :
	?>
	<div class="jobs-none-container">
		<p><?php awsm_no_jobs_msg(); ?></p>
	</div>
	<?php
endif;

/**
 * after_awsm_jobs_listing hook
 *
 * @since 1.1
 */
do_action( 'after_awsm_jobs_listing' );
