<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Dashboard_Widget {
	private static $instance = null;

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_setup' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function dashboard_setup() {
		wp_add_dashboard_widget( 'awsm-jobs-overview-dashboard', esc_html__( 'WP Job Openings - Overview', 'wp-job-openings' ), array( $this, 'display_widget' ) );
	}

	public function display_widget() {
		$overview_data = AWSM_Job_Openings::get_overview_data();
		$job_data      = $this->get_job_data();
		?>

		<div class="awsm-jobs-dashboard-wrapper">
			<div class="awsm-jobs-statistics">
				<div class="awsm-jobs-statistic">
					<span><?php echo esc_html( $overview_data['active_jobs'] ); ?></span>
					<?php esc_html_e( 'Active Jobs', 'wp-job-openings' ); ?>
				</div>
				<?php if ( current_user_can( 'edit_applications' ) ) : ?>
						<div class="awsm-jobs-statistic">
							<span><?php echo esc_html( $overview_data['new_applications'] ); ?></span>
							<?php esc_html_e( 'New Applications', 'wp-job-openings' ); ?>
						</div>
						<div class="awsm-jobs-statistic">
							<span><?php echo esc_html( $overview_data['total_applications'] ); ?></span>
							<?php esc_html_e( 'Total Applications', 'wp-job-openings' ); ?>
						</div>
				<?php endif; ?>
			</div>

			<?php
			if ( ! empty( $job_data ) ) :
				?>
				<h3><?php echo esc_html_e( 'Active Jobs', 'wp-job-openings' ); ?></h3>
				<table class="awsm-jobs-dashboard-table widefat">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Job Title', 'wp-job-openings' ); ?></th>
							<?php
							if ( current_user_can( 'edit_applications' ) ) {
								echo '<th>' . esc_html__( 'Applications', 'wp-job-openings' ) . '</th>';
							}
							?>
							<?php
							if ( current_user_can( 'edit_jobs' ) ) {
								echo '<th>' . esc_html__( 'Views', 'wp-job-openings' ) . '</th>';
							}
							?>
							<th><?php esc_html_e( 'Expiry', 'wp-job-openings' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ( $job_data as $data ) :
						?>
							<tr>
								<td>
									<?php
									if ( current_user_can( 'edit_post', $data['id'] ) ) {
										printf( '<a href="%2$s">%1$s</a>', esc_html( $data['title'] ), esc_url( get_edit_post_link( $data['id'] ) ) );
									} else {
										echo esc_html( $data['title'] );
									}
									?>
								</td>
								<?php
								if ( current_user_can( 'edit_applications' ) ) {
									printf( '<td><a href="%2$s">%1$s</a></td>', esc_html( $data['count'] ), esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $data['id'] ) ) );
								}

								if ( current_user_can( 'edit_jobs' ) ) {
									echo '<td>' . esc_html( $data['views'] ) . '</td>';
								}
								?>
								<td><?php echo ! empty( $data['expiry'] ) ? esc_html( $data['expiry'] ) : '<span aria-hidden="true">—</span>'; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( current_user_can( 'edit_jobs' ) || current_user_can( 'edit_applications' ) ) : ?>
					<div class="awsm-jobs-dashboard-btn-wrapper">
						<?php if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) && current_user_can( 'edit_others_applications' ) ) : ?>
							<a href="<?php echo esc_url( 'https://awsm.in/get/wpjo-pro/' ); ?>" class="awsm-jobs-dashboard-btn awsm-jobs-get-pro-btn button" target="_blank"><?php esc_html_e( 'Upgrade', 'wp-job-openings' ); ?></a>
						<?php endif; ?>
						<?php if ( current_user_can( 'edit_jobs' ) ) : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-overview' ) ); ?>" class="awsm-jobs-dashboard-btn button button-primary"><?php esc_html_e( 'View More', 'wp-job-openings' ); ?></a>
						<?php endif; ?>
						<?php if ( current_user_can( 'edit_applications' ) ) : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="awsm-jobs-dashboard-btn button button-link"><?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?></a>
						<?php endif; ?>
					</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function get_active_jobs( $numberjobs = 5 ) {
		$results = AWSM_Job_Openings_Overview::get_jobs(
			array(
				'numberjobs' => $numberjobs,
				'job_status' => 'publish',
			)
		);
		return $results;
	}

	public function get_job_data() {
		$job_data    = array();
		$active_jobs = self::get_active_jobs();
		if ( ! empty( $active_jobs ) ) {
			foreach ( $active_jobs as $job ) {
				$views          = intval( get_post_meta( $job->ID, 'awsm_views_count', true ) );
				$expiry         = '';
				$expiry_on_list = get_post_meta( $job->ID, 'awsm_set_exp_list', true );
				$job_expiry     = get_post_meta( $job->ID, 'awsm_job_expiry', true );

				if ( $expiry_on_list === 'set_listing' && ! empty( $job_expiry ) ) {
					$expiry = date_i18n( get_awsm_jobs_date_format( 'dashboard-widget' ), strtotime( $job_expiry ) );
				}

				$job_data[] = array(
					'id'     => $job->ID,
					'title'  => get_the_title( $job->ID ),
					'count'  => $job->applications_count,
					'views'  => $views,
					'expiry' => $expiry,
				);
			}
		}
		return $job_data;
	}
}

AWSM_Job_Openings_Dashboard_Widget::init();
