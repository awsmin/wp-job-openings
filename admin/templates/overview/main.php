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
	            <h3>
				<?php
							/* translators: %s: Current user name */
							printf( esc_html__( 'Hello %s!', 'wp-job-openings' ) . '<br>', esc_html( $user_obj->display_name ) );
                ?>
				</h3>
	            <p><?php if ( $active_jobs === 0 ) {
							esc_html_e( "Welcome to WP Job Openings! Let's get started?", 'wp-job-openings' );
						} else {
							if ( current_user_can( 'edit_others_applications' ) && $new_applications > 0 ) {
								/* translators: %s: New applications count */
								printf( esc_html__( 'You have %s new applications to review', 'wp-job-openings' ), esc_html( $new_applications ) );
							}
						}
						?></p>
	          <?php if ( $active_jobs === 0 ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ); ?>" class="button-wpjo"><?php esc_html_e( 'Add A New Opening', 'wp-job-openings' ); ?></a>
					<?php else : ?>
						<?php if ( current_user_can( 'edit_others_applications' ) && $total_applications > 0 ) : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="button-wpjo"><?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>" class="button-wpjo"><?php esc_html_e( 'View All Jobs', 'wp-job-openings' ); ?></a>
						<?php endif; ?>
					<?php endif; ?></div>
	         <!-- .awsm-jobs-overview-welcome-left -->
	         <div class="awsm-jobs-overview-welcome-right">
	            <ul>
	               <li>
	               		<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/icon-1.svg')?>" align="Icon">
						   <?php esc_html_e( 'Open Positions', 'wp-job-openings' ); ?>					
	                  	<span><?php echo esc_html( $active_jobs ); ?></span>
	               </li>
				   <?php if ( current_user_can( 'edit_applications' ) ) : ?>
	               <li>
	               		<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/icon-2.svg')?>" align="Icon">
						   <?php esc_html_e( 'New Applications', 'wp-job-openings' ); ?>
	                 	<span><?php echo esc_html( $new_applications ); ?></span>
	               </li>
	               <li>
	               		<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/icon-3.svg')?>" align="Icon">
						   <?php esc_html_e( 'Total Applications', 'wp-job-openings' ); ?>
	               		<span><?php echo esc_html( $total_applications ); ?></span>
	               </li>
				   <?php endif;?>
	            </ul>
	         </div>
	         <!-- .awsm-jobs-overview-welcome-right -->
	      </div>
	      <!-- .awsm-jobs-overview-welcome -->
	   </div>
	   <?php
			$screen        = get_current_screen();
			$columns       = absint( $screen->get_columns() );
			$columns_class = '';

		if ( $columns ) {
			$columns_class = " columns-{$columns}";
		}
		?>
	 <!-- .awsm-jobs-overview-row -->
	 <div class="awsm-jobs-overview-row">
	   		<div class="awsm-jobs-overview-col">
	   			<div class="awsm-jobs-overview-chart flex-item">
		   			<div class="awsm-jobs-overview-col-head">
		   				<h2>Application by Status</h2>
		   			</div><!-- .awsm-jobs-overview-col-head -->
		   			<div class="awsm-jobs-overview-col-content">
		   				
					   
					</div><!-- .awsm-jobs-overview-col-content -->
		   		</div>
		      	<!-- .awsm-jobs-overview-chart -->
		      </div>
		      <!-- .awsm-jobs-overview-col -->
		      <div class="awsm-jobs-overview-col">
		      	<div class="awsm-jobs-overview-chart flex-item">
		   			<div class="awsm-jobs-overview-col-head">
		   				<h2>Application Analysis</h2>
		   			</div><!-- .awsm-jobs-overview-col-head -->
		   			<div class="awsm-jobs-overview-col-content">
						<?php
					   $analytics_data = AWSM_Job_Openings_Overview::get_applications_analytics_data();
	if ( ! empty( $analytics_data ) ) :
		?>
		<div class="awsm-jobs-overview-chart-wrapper">
			<canvas id="awsm-jobs-overview-applications-analytics-chart"></canvas>
		</div>
	<?php else : ?>
		<div class="awsm-jobs-overview-empty-wrapper">
			<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/applications-analytics-chart.png' ); ?>">
			<p>📂 <?php esc_html_e( 'Awaiting applications', 'wp-job-openings' ); ?></p>
		</div>
		<?php
		endif;?>
		   				<!-- <img src="https://i.ibb.co/hgskbKd/Screenshot-2024-03-05-at-12-53-06-PM.png" alt="Alt text"> -->
		   			</div><!-- .awsm-jobs-overview-col-content -->
		   		</div>
		      	<!-- .awsm-jobs-overview-chart -->
		      </div>
		      <!-- .awsm-jobs-overview-col -->
		      <div class="awsm-jobs-overview-col">
		      	<div class="awsm-jobs-overview-get-started flex-item">
		   			<div class="awsm-jobs-overview-col-head">
		   				<h2>Get started</h2>
		   			</div><!-- .awsm-jobs-overview-col-head -->
		   			<div class="awsm-jobs-overview-col-content">
		   				<h3>Need help with something?</h3>
		   				<ul>
							<li><a href="https://docs.wpjobopenings.com/" target="_blank" rel="noopener">Plugin Documentation<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg></a></li>
							<li><a href="https://docs.wpjobopenings.com/developers/hooks" target="_blank" rel="noopener">Hooks &amp; Functions<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg></a></li>
							<li><a href="https://roadmap.wpjobopenings.com/boards/feedback" target="_blank" rel="noopener">Feedback<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg></a></li>
							<li><a href="https://roadmap.wpjobopenings.com/roadmap" target="_blank" rel="noopener">Roadmap<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg></a></li>
						</ul>
		   			</div><!-- .awsm-jobs-overview-col-content -->
		   		</div>
		      	<!-- .awsm-jobs-overview-chart -->
		      </div>
		      <!-- .awsm-jobs-overview-col -->
	   </div>
 <!-- .awsm-jobs-overview-row -->
 <div class="awsm-jobs-overview-row">
		      <div class="awsm-jobs-overview-col">
	   			<div class="awsm-jobs-overview-list flex-item">
		   			<div class="awsm-jobs-overview-col-head">
		   				<h2>Recent Applications</h2>
		   				<a href="edit.php?post_type=awsm_job_openings">View All<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M10.4525 5.62256L6.98345 2.15338C6.88442 2.05436 6.75243 2 6.6117 2C6.47081 2 6.33891 2.05443 6.23988 2.15338L5.92491 2.46843C5.82596 2.5673 5.77145 2.69936 5.77145 2.84017C5.77145 2.98091 5.82596 3.11742 5.92491 3.21629L7.94873 5.24456H1.51896C1.22906 5.24456 1 5.47152 1 5.76149V6.20688C1 6.49686 1.22906 6.74669 1.51896 6.74669H7.97169L5.92499 8.78629C5.82604 8.88532 5.77153 9.01379 5.77153 9.1546C5.77153 9.29525 5.82604 9.4256 5.92499 9.52455L6.23996 9.83857C6.33898 9.9376 6.47089 9.99157 6.61178 9.99157C6.75251 9.99157 6.8845 9.9369 6.98352 9.83787L10.4526 6.36878C10.5519 6.26944 10.6065 6.13683 10.6061 5.99586C10.6064 5.85443 10.5519 5.72174 10.4525 5.62256Z" fill="#161616"/></svg></a>
		   			</div><!-- .awsm-jobs-overview-col-head -->
		   			<div class="awsm-jobs-overview-col-content">
						<?php 
						$applications = AWSM_Job_Openings::get_recent_applications( 5, false );
						
						if ( ! empty( $applications ) ) :
						?>
                        <?php
				foreach ( $applications as $application ) :
					$applicant_email = get_post_meta( $application->ID, 'awsm_applicant_email', true );
					$avatar          = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 32 ), $application->ID );
					$edit_link       = AWSM_Job_Openings::get_application_edit_link( $application->ID );
					$submission_time = human_time_diff( get_the_time( 'U', $application->ID ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wp-job-openings' );
					?>
		   				<a href="<?php echo esc_url( $edit_link ); ?>" class="awsm-jobs-overview-list-item">
						   <?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		   					<p>
		   						<strong><?php echo esc_html( $application->post_title ); ?></strong>
		   						<?php echo esc_html( get_post_meta( $application->ID, 'awsm_apply_for', true ) ); ?>
		   					</p>
		   					<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg>
		   				</a>
		   				<?php 
						endforeach;					
					    endif;?>
		   			</div><!-- .awsm-jobs-overview-col-content -->
		   		</div>
		      	<!-- .awsm-jobs-overview-chart -->
		      </div>
		      <!-- .awsm-jobs-overview-col -->
        <!-- .awsm-jobs-overview-col -->
		<div class="awsm-jobs-overview-col">
	   			<div class="awsm-jobs-overview-list flex-item">
		   			<div class="awsm-jobs-overview-col-head">
		   				<h2>Open Job Positions</h2>
		   				<a href="#">View All<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M10.4525 5.62256L6.98345 2.15338C6.88442 2.05436 6.75243 2 6.6117 2C6.47081 2 6.33891 2.05443 6.23988 2.15338L5.92491 2.46843C5.82596 2.5673 5.77145 2.69936 5.77145 2.84017C5.77145 2.98091 5.82596 3.11742 5.92491 3.21629L7.94873 5.24456H1.51896C1.22906 5.24456 1 5.47152 1 5.76149V6.20688C1 6.49686 1.22906 6.74669 1.51896 6.74669H7.97169L5.92499 8.78629C5.82604 8.88532 5.77153 9.01379 5.77153 9.1546C5.77153 9.29525 5.82604 9.4256 5.92499 9.52455L6.23996 9.83857C6.33898 9.9376 6.47089 9.99157 6.61178 9.99157C6.75251 9.99157 6.8845 9.9369 6.98352 9.83787L10.4526 6.36878C10.5519 6.26944 10.6065 6.13683 10.6061 5.99586C10.6064 5.85443 10.5519 5.72174 10.4525 5.62256Z" fill="#161616"/></svg></a>
		   			</div><!-- .awsm-jobs-overview-col-head -->
		   			<div class="awsm-jobs-overview-col-content">
					   <?php
					$custom_posts = array(
						'posts_per_page'   => 5,
						'post_type'        => 'awsm_job_openings',
						'post_status'      => array( 'publish'));
					$jobs =get_posts( $custom_posts );
					
					foreach ( $jobs as $job ) :
						
						$job_title = get_the_title( $job->ID );
						?>
		   				<a href="" class="awsm-jobs-overview-list-item">
		   					<span class="count">45</span>
		   					<p>
		   						
								   <?php
							
							if ( current_user_can( 'edit_post', $job->ID ) ) {
								printf( '<strong>%1$s</strong>', esc_html( $job_title ) ) ;
							} else {
								echo esc_html( $job_title );
							}
							  
							  printf('Published on %1$s %2$s',esc_html(date('F',strtotime($job->post_date))),date('d',strtotime($job->post_date)));
							?>
								
		   						
		   					</p>
		   					<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/></svg>
		   				</a>
					<?php 
					 endforeach;
					?>
					</div>
				</div>
					</div>


	
</div>
