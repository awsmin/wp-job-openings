<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Dashboard_Widget {
	public function display_widget() {
		$job_data = $this->get_job_data();
		$count = wp_count_posts( 'awsm_job_openings' );
		$jobs_count = $count->publish;
		$count_details = wp_count_posts( 'awsm_job_application' );
		$new_applications = $count_details->publish;
		$args = array(
			'post_type' => 'awsm_job_application',
			'post_status' => array('publish', 'progress', 'shortlist', 'reject', 'select' ),
			'numberposts' => -1,
		);
		$total_applications = count( get_posts( $args ) );
		?>
		<div class="awsm-job-overview-wdiget-container">
			<table class="widefat">
				<tr>
					<td>
						<p><?php echo esc_attr( $jobs_count ); ?></p>
					</td>
					<td>
						<p><?php echo esc_attr( $new_applications ); ?></p>
					</td>
					<td>
						<p><?php echo esc_attr( $total_applications ); ?></p>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo esc_html_e( 'Active Jobs', 'wp-job-openings'); ?></p>
					</td>
					<td>
						<p><?php echo esc_html_e( 'New Applications', 'wp-job-openings'); ?></p>
					</td>
					<td>
						<p><?php echo esc_html_e( 'Total Applications', 'wp-job-openings'); ?></p>
					</td>
				</tr>
			</table>
			<?php
				if( ! empty( $job_data ) ) {
			?>
			<h3><?php echo esc_html_e( 'Active jobs', 'wp-job-openings'); ?></h3>
			<table width="100%" class="widefat">
				<tbody>
					<tr class="awsm-job-overview-widget-heading">
						<td>
							<p><?php echo esc_html_e( 'Job Title', 'wp-job-openings'); ?></p>
						</td>
						<td>
							<p><?php echo esc_html_e( 'Applications', 'wp-job-openings'); ?></p>
						</td>
						<td>
							<p><?php echo esc_html_e( 'Views', 'wp-job-openings'); ?></p>
						</td>
						<td>
							<p><?php echo esc_html_e( 'Expiry', 'wp-job-openings'); ?></p>
						</td>
					</tr>
					<?php 
					foreach ( $job_data as $value ) {
					?>
					<tr>
						<td>
							<p><?php echo esc_attr( $value['title'] ); ?></p>
						</td>
						<td>
							<p><?php echo esc_attr( $value['appplication_count'] ); ?></p>
						</td>
						<td>
							<p><?php echo esc_attr( $value['views'] ); ?></p>
						</td>
						<td>
							<p><?php echo esc_attr( $value['expiry'] );?></p>
						</td>
					</tr>
					<?php  }?>
				</tbody>
			</table>
			<div class="awsm-job-overview-widget-buttons">
				<?php
				if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
				?>
					<p><a href="<?php echo esc_url( 'https://1.envato.market/jjbEP' ); ?>" target="_blank" class="button awsm-export-applications-btn button-primary"><strong><?php esc_html_e( 'Get Pro', 'wp-job-openings' ); ?></strong></a></p>
				<?php
				}
				?>
					<p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="button awsm-export-applications-btn button-primary"><strong><?php esc_html_e( 'View Applications', 'wp-job-openings' ); ?></strong></a></p>
					<p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>" class="button awsm-export-applications-btn button-primary"><strong><?php esc_html_e( 'View all Jobs', 'wp-job-openings' ); ?></strong></a></p>
				
			</div>
		</div>
		<?php
		}
	}

	public function jobs_overview_dashboard_widget() {
		wp_add_dashboard_widget('awsm_job_overview_widget', 'Wp Job Openings - Overview', array( $this,'display_widget') );  
	}

	public function get_job_data( ) {
		$jobs = get_posts(
			array(
			'numberposts' => 5,
			'post_status' => 'any',
			'post_type' => 'awsm_job_openings',
			)
		);

		$job_details = array();
		foreach ( $jobs as $job ) {
			$job_id =  $job->ID;
			$applications = AWSM_Job_Openings::get_applications( $job_id );
			$application_count = count( $applications );
			$job_details['application_id'] = $job_id;
			$job_details['title']  =  $job->post_title;
			$job_details['appplication_count'] = $application_count;
			$job_details['views']  = get_post_meta($job_id, 'awsm_views_count', true );
			$job_details['expiry'] = get_post_meta($job_id, 'awsm_job_expiry', true );
			$job_data[] = $job_details;
		}
		if( ! empty( $job_data ) ) {
			usort( $job_data, function( $a,$b ){ 
			return $a['appplication_count'] < $b['appplication_count']; 
			} );
		}
		return $job_data;
	}
}

if( class_exists( "AWSM_Job_Openings_Dashboard_Widget" ) ) {
	$widget_class = new AWSM_Job_Openings_Dashboard_Widget();
}

if( isset( $widget_class ) ) {
	add_action('wp_dashboard_setup', array( &$widget_class, 'jobs_overview_dashboard_widget'), 100 );
}