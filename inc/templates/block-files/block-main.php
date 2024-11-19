<?php
/**
 * Main template part for job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes    = isset( $attributes ) ? $attributes : array();
$view          = isset( $attributes['layout'] ) ? $attributes['layout'] : get_option( 'awsm_jobs_listing_view' );
$awsm_filters  = get_option( 'awsm_jobs_filter' );
$listing_specs = isset( $attributes['other_options'] ) ? $attributes['other_options'] : '';
$listing_specs = awsm_block_job_filters_explode( $listing_specs );

/**
 * Fires before The Loop to query for jobs.
 *
 * @since 3.5.0
 *
 * @param array $attributes Attributes array from block side .
 */
do_action( 'before_awsm_block_jobs_listing_loop', $attributes );

while ( $query->have_posts() ) {
	$query->the_post();
	$job_details = get_awsm_job_details();

	$attrs  = awsm_jobs_block_listing_item_class( array( "awsm-b-{$view}-item" ) );
	$attrs .= sprintf( ' id="awsm-b-%1$s-item-%2$s"', esc_attr( $view ), esc_attr( $job_details['id'] ) );

	echo '<div ' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
		<?php echo ( $view === 'grid' ) ? sprintf( '<a href="%s" class="awsm-b-job-item">', esc_url( $job_details['permalink'] ) ) : '<div class="awsm-b-job-item">'; ?>
			<div class="awsm-b-<?php echo esc_attr( $view ); ?>-left-col">
				<?php
					do_action_deprecated( 'before_awsm_block_jobs_listing_left_col_content', array( $job_details['id'], $attributes ), '3.0.0', 'before_awsm_block_jobs_listing_title' );
				?>
				
				<?php
					$featured_image = 'thumbnail';
				if ( isset( $attributes['featured_image_size'] ) && $attributes['featured_image_size'] != '' ) {
					$featured_image = $attributes['featured_image_size'];
				}
					awsm_jobs_block_featured_image( true, $featured_image, '', $attributes );
				?>

				<h2 class="awsm-b-job-post-title">
					<?php
						$job_title = ( $view === 'grid' ) ? esc_html( $job_details['title'] ) : sprintf( '<a href="%2$s">%1$s</a>', esc_html( $job_details['title'] ), esc_url( $job_details['permalink'] ) );
						echo apply_filters( 'awsm_jobs_block_listing_title', $job_title, $view ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</h2>

				<?php
					/**
					 * after_awsm_jobs_listing_title hook
					 *
					 * @since 3.5.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'after_awsm_block_jobs_listing_title', $job_details['id'], $attributes );

					do_action_deprecated( 'after_awsm_block_jobs_listing_left_col_content', array( $job_details['id'], $attributes ), '3.0.0', 'after_awsm_block_jobs_listing_title' );
				?>
			</div>

			<div class="awsm-b-<?php echo esc_attr( $view ); ?>-right-col">
				<?php

					/**
					 * before_awsm_block_jobs_listing_specs_content hook
					 *
					 * @since 3.5.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'before_awsm_block_jobs_listing_specs_content', $job_details['id'], $attributes );

					do_action_deprecated( 'before_awsm_block_jobs_listing_right_col_content', array( $job_details['id'], $attributes ), '3.0.0', 'before_awsm_block_jobs_listing_specs_content' );

					awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs, false );

					awsm_job_more_details( $job_details['permalink'], $view );
				?>
			</div>
		<?php echo ( $view === 'grid' ) ? '</a>' : '</div>'; ?>
	<?php
	echo '</div>';
}

wp_reset_postdata();

awsm_block_jobs_load_more( $query, $attributes );
