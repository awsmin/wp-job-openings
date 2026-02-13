<?php
/**
 * Main template part for job openings
 *
 * Override this by copying it to currenttheme/wp-job-openings/job-openings/main.php
 *
 * @package wp-job-openings
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode_atts = isset( $shortcode_atts ) ? $shortcode_atts : array();
$view           = awsm_jobs_view( $shortcode_atts );
$awsm_filters   = get_option( 'awsm_jobs_filter' );
$listing_specs  = get_option( 'awsm_jobs_listing_specs' );

/**
 * Fires before The Loop to query for jobs.
 *
 * @since 1.1.0
 * @since 2.2.0 The `$shortcode_atts` parameter was added.
 *
 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
 */
do_action( 'before_awsm_jobs_listing_loop', $shortcode_atts );

while ( $query->have_posts() ) {
	$query->the_post();
	$job_details = get_awsm_job_details();

	$attrs  = awsm_jobs_listing_item_class( array( "awsm-{$view}-item" ) );
	$attrs .= sprintf( ' id="awsm-%1$s-item-%2$s"', esc_attr( $view ), esc_attr( $job_details['id'] ) );

	echo '<div ' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
		<?php echo ( $view === 'grid' ) ? sprintf( '<a href="%s" class="awsm-job-item">', esc_url( $job_details['permalink'] ) ) : '<div class="awsm-job-item">'; ?>
			<div class="awsm-<?php echo esc_attr( $view ); ?>-left-col">
				<?php
					/**
					 * before_awsm_jobs_listing_title hook
					 *
					 * @since 3.0.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'before_awsm_jobs_listing_title', $job_details['id'], $shortcode_atts );

					do_action_deprecated( 'before_awsm_jobs_listing_left_col_content', array( $job_details['id'], $shortcode_atts ), '3.0.0', 'before_awsm_jobs_listing_title' );
				?>

				<?php awsm_jobs_featured_image(); ?>

				<h2 class="awsm-job-post-title">
					<?php
						$job_title = ( $view === 'grid' ) ? esc_html( $job_details['title'] ) : sprintf( '<a href="%2$s">%1$s</a>', esc_html( $job_details['title'] ), esc_url( $job_details['permalink'] ) );
						echo apply_filters( 'awsm_jobs_listing_title', $job_title, $view ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</h2>

				<?php
					/**
					 * after_awsm_jobs_listing_title hook
					 *
					 * @since 3.0.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'after_awsm_jobs_listing_title', $job_details['id'], $shortcode_atts );

					do_action_deprecated( 'after_awsm_jobs_listing_left_col_content', array( $job_details['id'], $shortcode_atts ), '3.0.0', 'after_awsm_jobs_listing_title' );
				?>
			</div>

			<div class="awsm-<?php echo esc_attr( $view ); ?>-right-col">
				<?php
					/**
					 * before_awsm_jobs_listing_specs_content hook
					 *
					 * @since 3.0.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'before_awsm_jobs_listing_specs_content', $job_details['id'], $shortcode_atts );

					do_action_deprecated( 'before_awsm_jobs_listing_right_col_content', array( $job_details['id'], $shortcode_atts ), '3.0.0', 'before_awsm_jobs_listing_specs_content' );

					awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs, false );

					awsm_job_more_details( $job_details['permalink'], $view );

					/**
					 * after_awsm_jobs_listing_specs_content hook
					 *
					 * @since 3.0.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'after_awsm_jobs_listing_specs_content', $job_details['id'], $shortcode_atts );

					do_action_deprecated( 'after_awsm_jobs_listing_right_col_content', array( $job_details['id'], $shortcode_atts ), '3.0.0', 'after_awsm_jobs_listing_specs_content' );
				?>
			</div>
		<?php echo ( $view === 'grid' ) ? '</a>' : '</div>'; ?>
	<?php
	echo '</div>';
}

wp_reset_postdata();

/**
 * Fires after The Loop.
 *
 * @since 1.1.0
 * @since 2.2.0 The `$shortcode_atts` parameter was added.
 *
 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
 */
do_action( 'after_awsm_jobs_listing_loop', $shortcode_atts );

awsm_jobs_load_more( $query, $shortcode_atts );
