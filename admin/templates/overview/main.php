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
<h1></h1>
   <div class="awsm-jobs-overview">
		<div class="awsm-jobs-overview-row">
			<div class="awsm-jobs-overview-col awsm-jobs-overview-col-fw">
				<div class="awsm-jobs-overview-welcome">
					<div class="awsm-jobs-overview-welcome-left">
						<h3>
							<?php
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
			</div><!-- .awsm-jobs-overview-col -->
		</div><!-- .awsm-jobs-overview-row -->
		<div class="awsm-jobs-overview-row">
			<div class="awsm-jobs-overview-col">
				<div class="awsm-jobs-overview-chart flex-item">
					<div class="awsm-jobs-overview-col-head">
						<h2><?php esc_html_e( 'Application by Status', 'wp-job-openings' ); ?></h2>
					</div><!-- .awsm-jobs-overview-col-head -->
					<div class="awsm-jobs-overview-col-content">
						<?php
						if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
							// Translators: %1$s is the opening <a> tag for the PRO Plan link, %2$s is the closing </a> tag.
							$pro_link = sprintf( esc_html__( 'This feature requires %1$sPRO Plan%2$s to work', 'wp-job-openings' ), '<a href="https://awsm.in/get/wpjo-pro/">', '</a>' );
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							printf( '<div class="awsm-jobs-overview-widget-wrapper"><div class="awsm-jobs-pro-feature"><img src="%2$s"><p>%1$s</p></div></div>', $pro_link, esc_url( 'https://i.ibb.co/vXyz24d/Screenshot-2024-03-05-at-12-41-12-PM.png' ) );
						} else {
							// Ensure the AWSM_Job_Openings_Pro_Overview class is initialized
							$overview_instance = AWSM_Job_Openings_Pro_Overview::init();

							// Set the unique ID for which you want the template path
							$unique_id = 'awsm-jobs-overview-applications-by-status';

							// Get the template path
							$template_path = $overview_instance->pro_widget_template_path( '', $unique_id );

							// Include the template file if it exists
							if ( file_exists( $template_path ) ) {
								include $template_path;
							} else {
								echo '<p>' . esc_html__( 'Template not found.', 'wp-job-openings' ) . '</p>';
							}
						}
						?>
					</div><!-- .awsm-jobs-overview-col-content -->
				</div><!-- .awsm-jobs-overview-chart -->
			</div><!-- .awsm-jobs-overview-col -->
			<div class="awsm-jobs-overview-col" id="awsm-jobs-overview-applications-analytics">
				<div class="awsm-jobs-overview-chart flex-item">
					<div class="awsm-jobs-overview-col-head">
						<h2><?php esc_html_e( 'Application Analytics', 'wp-job-openings' ); ?></h2>
					</div><!-- .awsm-jobs-overview-col-head -->
					<div class="awsm-jobs-overview-col-content">
						<?php
							$widget_id = 'awsm-jobs-overview-applications-by-analytics';
							// Include your template here
							require AWSM_JOBS_PLUGIN_DIR . '/admin/templates/overview/widgets/applications-analytics.php';
						?>
							<!-- Replace this image with chart.js -->
					</div><!-- .awsm-jobs-overview-col-content -->
				</div><!-- .awsm-jobs-overview-chart -->
			</div><!-- .awsm-jobs-overview-col -->
			<div class="awsm-jobs-overview-col">
				<?php
					$get_started_links = array(
						array(
							'id'        => 'documentation',
							'url'       => 'https://docs.wpjobopenings.com/',
							'link_text' => __( 'Plugin Documentation', 'wp-job-openings' ),
						),
						array(
							'id'        => 'hooks',
							'url'       => 'https://docs.wpjobopenings.com/developers/hooks',
							'link_text' => __( 'Hooks & Functions', 'wp-job-openings' ),
						),
						array(
							'id'        => 'feedback',
							'url'       => 'https://roadmap.wpjobopenings.com/boards/feedback',
							'link_text' => __( 'Feedback', 'wp-job-openings' ),
						),
						array(
							'id'        => 'roadmap',
							'url'       => 'https://roadmap.wpjobopenings.com/roadmap',
							'link_text' => __( 'Roadmap', 'wp-job-openings' ),
						),
						array(
							'id'        => 'support',
							'url'       => 'https://wordpress.org/support/plugin/wp-job-openings/',
							'link_text' => __( 'Get Support', 'wp-job-openings' ),
						),
					);
					if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
						$support_link_key = array_search( 'support', wp_list_pluck( $get_started_links, 'id' ) );
						if ( $support_link_key !== false ) {
							unset( $get_started_links[ $support_link_key ] );
						}
					}

					/**
						* Filters the overview get started widget links.
						*
						* @since 3.0.0
						*
						* @param array $get_started_links Links data array.
						*/
					$get_started_links = apply_filters( 'awsm_jobs_overview_get_started_widget_links', $get_started_links );
					?>
				<div class="awsm-jobs-overview-get-started flex-item">
					<div class="awsm-jobs-overview-col-head">
						<h2><?php esc_html_e( 'Get started', 'wp-job-openings' ); ?></h2>
					</div><!-- .awsm-jobs-overview-col-head -->
					<div class="awsm-jobs-overview-col-content">
						<h3><?php esc_html_e( 'Need help with something?', 'wp-job-openings' ); ?></h3>
						<?php
							$svg = '<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg>';
						?>
						<ul>
							<?php
							foreach ( $get_started_links as $gs_link ) {
								printf( '<li><a href="%2$s" target="_blank" rel="noopener">%1$s%3$s</a></li>', esc_html( $gs_link['link_text'] ), esc_url( $gs_link['url'] ), $svg );
							}
							?>
						</ul>
					</div><!-- .awsm-jobs-overview-col-content -->
				</div><!-- .awsm-jobs-overview-get-started -->
			</div><!-- .awsm-jobs-overview-col -->
		</div><!-- .awsm-jobs-overview-row -->
		<div class="awsm-jobs-overview-row">
			<div class="awsm-jobs-overview-col">
				<div class="awsm-jobs-overview-list flex-item">
					<div class="awsm-jobs-overview-col-head">
						<h2><?php esc_html_e( 'Recent Applications', 'wp-job-openings' ); ?></h2>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>">
							<?php esc_html_e( 'View All', 'wp-job-openings' ); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M10.4525 5.62256L6.98345 2.15338C6.88442 2.05436 6.75243 2 6.6117 2C6.47081 2 6.33891 2.05443 6.23988 2.15338L5.92491 2.46843C5.82596 2.5673 5.77145 2.69936 5.77145 2.84017C5.77145 2.98091 5.82596 3.11742 5.92491 3.21629L7.94873 5.24456H1.51896C1.22906 5.24456 1 5.47152 1 5.76149V6.20688C1 6.49686 1.22906 6.74669 1.51896 6.74669H7.97169L5.92499 8.78629C5.82604 8.88532 5.77153 9.01379 5.77153 9.1546C5.77153 9.29525 5.82604 9.4256 5.92499 9.52455L6.23996 9.83857C6.33898 9.9376 6.47089 9.99157 6.61178 9.99157C6.75251 9.99157 6.8845 9.9369 6.98352 9.83787L10.4526 6.36878C10.5519 6.26944 10.6065 6.13683 10.6061 5.99586C10.6064 5.85443 10.5519 5.72174 10.4525 5.62256Z" fill="#161616"/>
							</svg>
						</a>
					</div><!-- .awsm-jobs-overview-col-head -->
				<?php
					$applications = AWSM_Job_Openings::get_recent_applications( 10, false );
				if ( ! empty( $applications ) ) :
					foreach ( $applications as $application ) :
						$applicant_email = get_post_meta( $application->ID, 'awsm_applicant_email', true );
						$avatar          = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 130 ) );
						$avatar          = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 36 ), $application->ID );
						$edit_link       = AWSM_Job_Openings::get_application_edit_link( $application->ID );
						$submission_time = human_time_diff( get_the_time( 'U', $application->ID ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wp-job-openings' );
						?>
					<a href="<?php echo esc_url( $edit_link ); ?>" class="awsm-jobs-overview-list-item">
						<?php echo $avatar; ?>
						<p>
							<strong><?php echo esc_html( $application->post_title ); ?></strong>
						<?php echo esc_html( get_post_meta( $application->ID, 'awsm_apply_for', true ) ); ?>
						</p>
						<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin">
							<path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/>
						</svg>
					</a>
					<?php endforeach; ?>
					<?php else : ?>
					<div class="awsm-jobs-overview-empty-wrapper">
						<p>📂 <?php esc_html_e( 'Awaiting applications', 'wp-job-openings' ); ?></p>
					</div>
					<?php endif; ?>
				</div><!-- .awsm-jobs-overview-list -->
			</div><!-- .awsm-jobs-overview-col -->
			<div class="awsm-jobs-overview-col">
				<div class="awsm-jobs-overview-list flex-item">
					<div class="awsm-jobs-overview-col-head">
						<h2><?php esc_html_e( 'Open Job Positions', 'wp-job-openings' ); ?></h2>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>">
							<?php esc_html_e( 'View All', 'wp-job-openings' ); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M10.4525 5.62256L6.98345 2.15338C6.88442 2.05436 6.75243 2 6.6117 2C6.47081 2 6.33891 2.05443 6.23988 2.15338L5.92491 2.46843C5.82596 2.5673 5.77145 2.69936 5.77145 2.84017C5.77145 2.98091 5.82596 3.11742 5.92491 3.21629L7.94873 5.24456H1.51896C1.22906 5.24456 1 5.47152 1 5.76149V6.20688C1 6.49686 1.22906 6.74669 1.51896 6.74669H7.97169L5.92499 8.78629C5.82604 8.88532 5.77153 9.01379 5.77153 9.1546C5.77153 9.29525 5.82604 9.4256 5.92499 9.52455L6.23996 9.83857C6.33898 9.9376 6.47089 9.99157 6.61178 9.99157C6.75251 9.99157 6.8845 9.9369 6.98352 9.83787L10.4526 6.36878C10.5519 6.26944 10.6065 6.13683 10.6061 5.99586C10.6064 5.85443 10.5519 5.72174 10.4525 5.62256Z" fill="#161616"/>
							</svg>
						</a>
					</div><!-- .awsm-jobs-overview-col-head -->
					<div class="awsm-jobs-overview-col-content">
					<?php
					if ( ! empty( $jobs ) ) :
						foreach ( $jobs as $job ) :
							$jobmeta     = get_post_meta( $job->ID );
							$expiry_date = isset( $jobmeta['awsm_job_expiry'][0] ) ? $jobmeta['awsm_job_expiry'][0] : null;

							// Check if the job is not expired
							if ( ! $expiry_date || strtotime( $expiry_date ) >= strtotime( current_time( 'Y-m-d' ) ) ) :
								$job_title      = get_the_title( $job->ID );
								$published_date = get_the_date( 'F j, Y', $job->ID );
								?>
									<a href="<?php echo esc_url( get_edit_post_link( $job->ID ) ); ?>" class="awsm-jobs-overview-list-item">
										<span class="count"><?php echo esc_html( $job->applications_count ); ?></span>
										<p>
											<strong>
										<?php
										if ( current_user_can( 'edit_post', $job->ID ) ) {
											printf( '<span>%1$s</span>', esc_html( $job_title ) );
										} else {
											echo esc_html( $job_title );
										}
										?>
											</strong>
										<?php printf( esc_html__( 'Published on: %s', 'wp-job-openings' ), sprintf( esc_html( $published_date ) ) ); ?>
										</p>
										<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin">
											<path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/>
										</svg>
									</a>
									<?php
								endif;
							endforeach;
						else :
							?>
							<div class="awsm-jobs-overview-empty-wrapper">
								<p>💼
									<?php
										/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
										printf( '&nbsp;' . esc_html__( 'Looks empty! %1$sAdd some%2$s', 'wp-job-openings' ), '<a href="' . esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ) . '">', '</a>' );
									?>
								</p>
							</div>
						<?php endif; ?>
					</div>
				</div><!-- .awsm-jobs-overview-list -->
			</div><!-- .awsm-jobs-overview-col -->
			<div class="awsm-jobs-overview-col">
				   <div class="awsm-jobs-overview-list awsm-jobs-overview-list-interview flex-item">
					   <div class="awsm-jobs-overview-col-head">
						   <h2><?php esc_html_e( 'Add-ons for HireSuit', 'wp-job-openings' ); ?></h2>
						   <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-add-ons' ) ); ?>">View All<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M10.4525 5.62256L6.98345 2.15338C6.88442 2.05436 6.75243 2 6.6117 2C6.47081 2 6.33891 2.05443 6.23988 2.15338L5.92491 2.46843C5.82596 2.5673 5.77145 2.69936 5.77145 2.84017C5.77145 2.98091 5.82596 3.11742 5.92491 3.21629L7.94873 5.24456H1.51896C1.22906 5.24456 1 5.47152 1 5.76149V6.20688C1 6.49686 1.22906 6.74669 1.51896 6.74669H7.97169L5.92499 8.78629C5.82604 8.88532 5.77153 9.01379 5.77153 9.1546C5.77153 9.29525 5.82604 9.4256 5.92499 9.52455L6.23996 9.83857C6.33898 9.9376 6.47089 9.99157 6.61178 9.99157C6.75251 9.99157 6.8845 9.9369 6.98352 9.83787L10.4526 6.36878C10.5519 6.26944 10.6065 6.13683 10.6061 5.99586C10.6064 5.85443 10.5519 5.72174 10.4525 5.62256Z" fill="#161616"/></svg></a>
					   </div><!-- .awsm-jobs-overview-col-head -->
					   <div class="awsm-jobs-overview-col-content">
						   <div class="awsm-wpjo-addon-items">
						   <?php
							$allowed_html = array(
								'a'      => array(
									'href'  => array(),
									'title' => array(),
								),
								'p'      => array(),
								'br'     => array(),
								'em'     => array(),
								'span'   => array(),
								'strong' => array(),
								'small'  => array(),
							);
							$json         = get_transient( '_awsm_add_ons_data' );
							$add_ons_data = json_decode( $json, true );

							if ( ! empty( $add_ons_data ) && is_array( $add_ons_data ) ) :
								$add_ons_data = array_slice( $add_ons_data, 0, 5 );

								foreach ( $add_ons_data as $add_on ) :
									$add_on_type = $add_on['pricing']['type'];
									?>
									<div class="awsm-wpjo-addon-item">
										<div class="awsm-wpjo-addon-item-head">
											<div>
												<h3><?php echo esc_html( $add_on['name'] ); ?></h3>
												<p><?php echo esc_html__( 'Price starting from', 'wp-job-openings' ); ?> 
													<strong>
														<?php
														$price_label = $add_on_type === 'free' || empty( $add_on['pricing']['price'] )
															? __( 'Free', 'wp-job-openings' )
															: $add_on['pricing']['price'];
														echo esc_html( $price_label );
														?>
													</strong>
												</p>
											</div>
											<?php
											if ( current_user_can( 'install_plugins' ) ) {
												if ( empty( $add_on['wp_plugin'] ) && sanitize_title( $add_on['name'] ) === 'wp-job-openings-pro-pack' ) {
													$add_on['wp_plugin'] = 'pro-pack-for-wp-job-openings/pro-pack.php';
												}

												if ( ! empty( $add_on['wp_plugin'] ) || ! empty( $add_on['url'] ) ) {
													$add_on_details = array(
														'type' => $add_on_type,
														'url'  => $add_on['url'],
													);
													$awsm_info      = new AWSM_Job_Openings_Info();
													echo $awsm_info->get_add_on_btn_content(
														$add_on['wp_plugin'],
														$add_on_details
													); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												} else {
													printf(
														'<p>%s</p>',
														esc_html__( 'Coming soon!', 'wp-job-openings' )
													);
												}
											}
											?>
																					</div><!-- .awsm-wpjo-addon-item-head -->
										<p><?php echo wp_kses( $add_on['content'], $allowed_html ); ?></p>
									</div><!-- .awsm-wpjo-addon-item -->
									<?php
								endforeach;
							else :
								?>
								<div class="awsm-col">
									<div class="awsm-welcome-point-content">
										<p>
										<?php
										esc_html_e(
											'Sorry! Error fetching add-ons data. Please come back later.',
											'wp-job-openings'
										);
										?>
										</p>
									</div><!-- .awsm-welcome-point-image -->
								</div><!-- .col-->
							<?php endif; ?>

						   </div><!-- .awsm-wpjo-addon-items -->
					   </div><!-- .awsm-jobs-overview-col-content -->
				   </div>
				  <!-- .awsm-jobs-overview-chart -->
			</div>
		</div><!-- .awsm-jobs-overview-row -->
	   </div>
</div>

	
