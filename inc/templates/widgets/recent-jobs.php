<?php
/**
 * Template part for recent jobs widget
 *
 * Override this by copying it to currenttheme/wp-job-openings/widgets/recent-jobs.php
 *
 * @package wp-job-openings
 * @since 1.4
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$awsm_filters  = get_option( 'awsm_jobs_filter' );
$listing_specs = get_option( 'awsm_jobs_listing_specs' );
?>

<div class="awsm-job-wrap">
	<div class="awsm-job-listings awsm-lists">
		<?php
		/**
		 * before_awsm_recent_jobs_widget_loop hook
		 *
		 * Fires before The Loop for recent jobs widget
		 *
		 * @since 1.4
		 */
		do_action( 'before_awsm_recent_jobs_widget_loop', $args, $instance );

		while ( $query->have_posts() ) :
			$query->the_post();
			$job_details = get_awsm_job_details();
			?>
			<div class="awsm-list-item" id="awsm-list-item-<?php echo esc_attr( $job_details['id'] ); ?>">
				<div class="awsm-job-item">
					<div class="awsm-list-left-col">
						<?php
							/**
							 * before_awsm_recent_jobs_widget_title hook
							 *
							 * @since 3.0.0
							 */
							do_action( 'before_awsm_recent_jobs_widget_title', $args, $instance );

							do_action_deprecated( 'before_awsm_recent_jobs_widget_left_col_content', array( $args, $instance ), '3.0.0', 'before_awsm_recent_jobs_widget_title' );
						?>

						<h2 class="awsm-job-post-title">
							<?php
								$job_title = sprintf( '<a href="%2$s">%1$s</a>', esc_html( $job_details['title'] ), esc_url( $job_details['permalink'] ) );
								echo apply_filters( 'awsm_jobs_listing_title', $job_title, 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</h2>

						<?php
							/**
							 * after_awsm_recent_jobs_widget_title hook
							 *
							 * @since 3.0.0
							 */
							do_action( 'after_awsm_recent_jobs_widget_title', $args, $instance );

							do_action_deprecated( 'after_awsm_recent_jobs_widget_left_col_content', array( $args, $instance ), '3.0.0', 'after_awsm_recent_jobs_widget_title' );
						?>
					</div>

					<div class="awsm-list-right-col">
						<?php
							/**
							 * before_awsm_recent_jobs_widget_specs_content hook
							 *
							 * @since 3.0.0
							 */
							do_action( 'before_awsm_recent_jobs_widget_specs_content', $args, $instance );

							do_action_deprecated( 'before_awsm_recent_jobs_widget_right_col_content', array( $args, $instance ), '3.0.0', 'before_awsm_recent_jobs_widget_specs_content' );

						if ( $show_spec ) {
							awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs );
						}

						if ( $show_more ) {
							awsm_job_more_details( $job_details['permalink'], 'list' );
						}

							/**
							 * after_awsm_recent_jobs_widget_specs_content hook
							 *
							 * @since 3.0.0
							 */
							do_action( 'after_awsm_recent_jobs_widget_specs_content', $args, $instance );

							do_action_deprecated( 'after_awsm_recent_jobs_widget_right_col_content', array( $args, $instance ), '3.0.0', 'after_awsm_recent_jobs_widget_specs_content' );
						?>
					</div>
				</div>
			</div>
			<?php
		endwhile;

		wp_reset_postdata();

		/**
		 * after_awsm_recent_jobs_widget_loop hook
		 *
		 * Fires after The Loop for recent jobs widget
		 *
		 * @since 1.4
		 */
		do_action( 'after_awsm_recent_jobs_widget_loop', $args, $instance );
		?>
	</div>
</div>
