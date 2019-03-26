<?php
/**
 * Template part for recent jobs widget
 *
 * Override this by copying it to currenttheme/wp-job-openings/widgets/recent-jobs.php
 *
 * @package wp-job-openings
 * @since 1.4
 * @version 1.4
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
		do_action( 'before_awsm_recent_jobs_widget_loop', $args, $instance );

		while ( $query->have_posts() ) :
			$query->the_post();
			$job_details = get_awsm_job_details();
			?>
			<div class="awsm-list-item" id="awsm-list-item-<?php echo esc_attr__( $job_details['id'] ); ?>">
				<div class="awsm-job-item">
					<div class="awsm-list-left-col">
						<?php do_action( 'before_awsm_recent_jobs_widget_left_col_content', $args, $instance ); ?>

						<h2 class="awsm-job-post-title">
							<?php
								$title = sprintf( '<a href="%2$s">%1$s</a>', $job_details['title'], $job_details['permalink'] );
								echo apply_filters( 'awsm_jobs_listing_title', $title, 'list' );
							?>
						</h2>

						<?php do_action( 'after_awsm_recent_jobs_widget_left_col_content', $args, $instance ); ?>
					</div>

					<div class="awsm-list-right-col">
						<?php
							do_action( 'before_awsm_recent_jobs_widget_right_col_content', $args, $instance );

						if ( $show_spec ) {
							awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs );
						}

						if ( $show_more ) {
							awsm_job_more_details( $job_details['permalink'], 'list' );
						}

							do_action( 'after_awsm_recent_jobs_widget_right_col_content', $args, $instance );
						?>
					</div>
				</div>
			</div>
			<?php
		endwhile;

		wp_reset_postdata();

		do_action( 'after_awsm_recent_jobs_widget_loop', $args, $instance );
		?>
	</div>
</div>
