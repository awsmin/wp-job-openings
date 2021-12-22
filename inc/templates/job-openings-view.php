<?php
/**
 * Template for displaying job openings (used by shortcode too)
 *
 * Override this by copying it to currenttheme/wp-job-openings/job-openings-view.php
 *
 * @package wp-job-openings
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode_atts = isset( $shortcode_atts ) ? $shortcode_atts : array();

/**
 * Fires before the job listing content.
 *
 * @hooked awsm_jobs_archive_title
 *
 * @since 1.1.0
 * @since 2.2.0 The `$shortcode_atts` parameter was added.
 *
 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
 */
do_action( 'before_awsm_jobs_listing', $shortcode_atts );

$query = awsm_jobs_query( $shortcode_atts );

if ( $query->have_posts() ) : ?>
	<div class="awsm-job-wrap<?php awsm_jobs_wrapper_class(); ?>">

		<?php
			/**
			 * awsm_filter_form hook
			 *
			 * Display filter form for job listings
			 *
			 * @hooked AWSM_Job_Openings_Filters::display_filter_form()
			 *
			 * @since 1.0.0
			 * @since 1.3.0 The `$shortcode_atts` parameter was added.
			 *
			 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
			 */
			do_action( 'awsm_filter_form', $shortcode_atts );
		?>

		<div <?php awsm_jobs_view_class( '', $shortcode_atts ); ?><?php awsm_jobs_data_attrs( array(), $shortcode_atts ); ?>>
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
 * Fires after the job listing content.
 *
 * @since 1.1.0
 * @since 2.2.0 The `$shortcode_atts` parameter was added.
 *
 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
 */
do_action( 'after_awsm_jobs_listing', $shortcode_atts );
