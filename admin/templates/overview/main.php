<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_obj           = wp_get_current_user();
$overview_data      = AWSM_Job_Openings::get_overview_data();
$active_jobs        = intval( $overview_data['active_jobs'] );
$new_applications   = intval( $overview_data['new_applications'] );
$total_applications = intval( $overview_data['total_applications'] );

// Enable meta-box support.
do_action( 'add_meta_boxes_' . AWSM_Job_Openings_Overview::$screen_id, null );
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Job Openings Overview', 'wp-job-openings' ); ?></h1>

	<div class="awsm-jobs-overview">
		<div class="awsm-jobs-overview-row">
			<div class="awsm-jobs-overview-col awsm-jobs-overview-welcome">
				<div class="awsm-jobs-overview-welcome-left">
					<p>
						<?php
							/* translators: %s: Current user name */
							printf( esc_html__( 'Hi %s!', 'wp-job-openings' ) . '<br>', esc_html( $user_obj->display_name ) );

						if ( $active_jobs === 0 ) {
							esc_html_e( "Welcome to WP Job Openings! Let's get started?", 'wp-job-openings' );
						} else {
							if ( current_user_can( 'edit_others_applications' ) && $new_applications > 0 ) {
								/* translators: %s: New applications count */
								printf( esc_html__( 'You have %s new applications to review', 'wp-job-openings' ), esc_html( $new_applications ) );
							}
						}
						?>
					</p>
					<?php if ( $active_jobs === 0 ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'Add A New Opening', 'wp-job-openings' ); ?></a>
					<?php else : ?>
						<?php if ( current_user_can( 'edit_others_applications' ) && $total_applications > 0 ) : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'View All Jobs', 'wp-job-openings' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div><!-- .awsm-jobs-overview-welcome-left -->
				<div class="awsm-jobs-overview-welcome-right">
					<ul>
						<li>
							<span><?php echo esc_html( $active_jobs ); ?></span>
							<?php esc_html_e( 'Open Positions', 'wp-job-openings' ); ?>
						</li>

						<?php if ( current_user_can( 'edit_applications' ) ) : ?>
						<li>
							<span><?php echo esc_html( $new_applications ); ?></span>
							<?php esc_html_e( 'New Applications', 'wp-job-openings' ); ?>
						</li>
						<li>
							<span><?php echo esc_html( $total_applications ); ?></span>
							<?php esc_html_e( 'Total Applications', 'wp-job-openings' ); ?>
						</li>
						<?php endif; ?>
					</ul>
				</div><!-- .awsm-jobs-overview-welcome-right -->
			</div><!-- .awsm-jobs-overview-welcome -->
		</div><!-- .awsm-jobs-overview-row -->
	</div><!-- .awsm-jobs-overview -->

	<div class="awsm-jobs-overview-mb-wrapper">
		<?php
			$screen        = get_current_screen();
			$columns       = absint( $screen->get_columns() );
			$columns_class = '';

		if ( $columns ) {
			$columns_class = " columns-{$columns}";
		}
		?>
		<div id="dashboard-widgets" class="metabox-holder<?php echo esc_attr( $columns_class ); ?>">
			<div id="postbox-container-1" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
			</div>
			<div id="postbox-container-3" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
			</div>
			<div id="postbox-container-4" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
			</div>
		</div>

		<?php
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		?>
	</div><!-- .awsm-jobs-overview-mb-wrapper -->
</div>
