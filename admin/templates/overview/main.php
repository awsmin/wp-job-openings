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
	<h1><?php esc_html_e( 'Dashboard Overview', 'wp-job-openings' ); ?></h1>
	<div class="awsm-jobs-overview">
	   	<div class="awsm-jobs-overview-row">
			<div class="awsm-jobs-overview-col awsm-jobs-overview-welcome">
				<div class="awsm-jobs-overview-welcome-left">
					<h3><?php
					/* translators: %s: Current user name */
					printf( esc_html__( 'Hello %s!', 'wp-job-openings' ) . '<br>', esc_html( $user_obj->display_name ) );
					?>
					</h3>
					<p>
					<?php
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
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ); ?>" class="awsm-jobs-button"><?php esc_html_e( 'Add A New Opening', 'wp-job-openings' ); ?></a>
					<?php else : ?>
						<?php if ( current_user_can( 'edit_others_applications' ) && $total_applications > 0 ) : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="awsm-jobs-button"><?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>" class="awsm-jobs-button"><?php esc_html_e( 'View All Jobs', 'wp-job-openings' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div><!-- .awsm-jobs-overview-welcome-left -->
				<div class="awsm-jobs-overview-welcome-right">
					<ul>
					<li>
							<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/icon-1.svg' ); ?>" align="Icon">
							<?php esc_html_e( 'Open Positions', 'wp-job-openings' ); ?>					
							<span><?php echo esc_html( $active_jobs ); ?></span>
					</li>
					<?php if ( current_user_can( 'edit_applications' ) ) : ?>
					<li>
							<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/icon-2.svg' ); ?>" align="Icon">
							<?php esc_html_e( 'New Applications', 'wp-job-openings' ); ?>
							<span><?php echo esc_html( $new_applications ); ?></span>
					</li>
					<li>
							<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/icon-3.svg' ); ?>" align="Icon">
							<?php esc_html_e( 'Total Applications', 'wp-job-openings' ); ?>
							<span><?php echo esc_html( $total_applications ); ?></span>
					</li>
					<?php endif; ?>
					</ul>
				</div><!-- .awsm-jobs-overview-welcome-right -->
			</div><!-- .awsm-jobs-overview-welcome -->
		</div><!-- .awsm-jobs-overview-row -->
		
		<div class="awsm-jobs-overview-row">
	   		<div class="awsm-jobs-overview-col">
	   			<div class="awsm-jobs-overview-chart flex-item">
		   			<div class="awsm-jobs-overview-col-head">
		   				<h2><?php esc_html_e( 'Application by Status', 'wp-job-openings' ); ?></h2>
		   			</div><!-- .awsm-jobs-overview-col-head -->
					<div class="awsm-jobs-overview-col-content">
		   				<!-- Replace this image with chart.js -->
		   				<img src="https://i.ibb.co/vXyz24d/Screenshot-2024-03-05-at-12-41-12-PM.png" alt="Alt text">
		   			</div><!-- .awsm-jobs-overview-col-content -->
				</div><!-- .awsm-jobs-overview-chart -->
			</div><!-- .awsm-jobs-overview-col -->
			<div class="awsm-jobs-overview-col">
		      	<div class="awsm-jobs-overview-chart flex-item">
		   			<div class="awsm-jobs-overview-col-head">
                       <h2><?php esc_html_e( 'Application Analytics', 'wp-job-openings' ); ?></h2>
		   			</div><!-- .awsm-jobs-overview-col-head -->
					<div class="awsm-jobs-overview-col-content">
                        <?php 
                         if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' )  ) {
                            $pro_link = sprintf( esc_html__( 'This feature requires %1$sPRO Plan%2$s to work', 'wp-job-openings' ), '<a href="https://awsm.in/get/wpjo-pro/">', '</a>' );
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            printf( '<div class="awsm-jobs-overview-widget-wrapper"><div class="awsm-jobs-pro-feature"><img src="%2$s"><p>%1$s</p></div></div>', $pro_link, esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/Screenshot-2024-03-05-at-12-53-06-PM.png' ) );
                         }
                        
                        ?>
		   				<!-- Replace this image with chart.js -->
		   			</div><!-- .awsm-jobs-overview-col-content -->
				</div><!-- .awsm-jobs-overview-chart -->
			</div><!-- .awsm-jobs-overview-col -->
		</div><!-- .awsm-jobs-overview-row -->
	</div>
</div>
	
