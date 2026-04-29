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
		if ( current_user_can( 'edit_jobs' ) ) {
			wp_add_dashboard_widget( 'awsm-jobs-overview-dashboard', esc_html__( 'Hirezoot - Overview', 'wp-job-openings' ), array( $this, 'display_widget' ) );
		}
	}

	public function display_widget() {
		$overview_data             = AWSM_Job_Openings::get_overview_data();
		$active_jobs               = intval( $overview_data['active_jobs'] );
		$total_active_applications = intval( isset( $overview_data['active_applications'] ) ? $overview_data['active_applications'] : 0 );
		$applications_count        = intval( isset( $overview_data['unread_applications'] ) ? $overview_data['unread_applications'] : 0 );
		$job_data                  = $this->get_job_data();

		?>
		<div class="awsm-jobs-dw-wrap">
			<!-- Stats Bar -->
			<div class="awsm-jobs-dw-stats">
				<div class="awsm-jobs-dw-stat">
					<div class="awsm-jobs-dw-stat-label"><?php esc_html_e( 'Open Positions', 'wp-job-openings' ); ?></div>
					<div class="awsm-jobs-dw-stat-value"><?php echo esc_html( $active_jobs ); ?></div>
				</div>
				<?php if ( current_user_can( 'edit_applications' ) ) : ?>
				<div class="awsm-jobs-dw-stat">
					<div class="awsm-jobs-dw-stat-label"><?php esc_html_e( 'New Applications', 'wp-job-openings' ); ?></div>
					<div class="awsm-jobs-dw-stat-value"><?php echo esc_html( $applications_count ); ?></div>
				</div>
				<div class="awsm-jobs-dw-stat">
					<div class="awsm-jobs-dw-stat-label"><?php esc_html_e( 'Total Applications', 'wp-job-openings' ); ?></div>
					<div class="awsm-jobs-dw-stat-value"><?php echo esc_html( $total_active_applications ); ?></div>
				</div>
				<?php endif; ?>
			</div>

			<!-- Active Jobs List -->
			<?php if ( ! empty( $job_data ) ) : ?>
				<div class="awsm-jobs-dw-section-title"><?php esc_html_e( 'Active Jobs', 'wp-job-openings' ); ?></div>
				<ul class="awsm-jobs-dw-jobs-list">
					<?php foreach ( $job_data as $data ) : ?>
						<li>
							<a href="<?php echo esc_url( get_edit_post_link( $data['id'] ) ); ?>" class="awsm-jobs-dw-overview-list-item">
								<span class="count"><?php echo esc_html( $data['count'] > 99 ? '99+' : $data['count'] ); ?></span>
								<p>
									<strong>
										<?php
										if ( current_user_can( 'edit_post', $data['id'] ) ) {
											printf( '<span>%s</span>', esc_html( $data['title'] ) );
										} else {
											echo esc_html( $data['title'] );
										}
										?>
									</strong>
									<?php
									/* translators: %s: Published date */
									printf( esc_html__( 'Published on: %s', 'wp-job-openings' ), esc_html( get_the_date( 'F j, Y', $data['id'] ) ) );
									?>
								</p>
								<svg width="4.922" height="8.333" viewBox="0 0 4.922 8.333" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin">
									<path d="M0.41139,0.133199 L0.13652,0.406167 C0.05068,0.492077 0.00339,0.606377 0.00339,0.728527 C0.00339,0.850617 0.05068,0.965047 0.13652,1.050957 L3.2505,4.164807 L0.13306,7.282237 C0.04722,7.368017 4.4408921e-16,7.482447 4.4408921e-16,7.604537 C4.4408921e-16,7.726617 0.04722,7.841117 0.13306,7.926967 L0.40624,8.199997 C0.58388,8.377777 0.87325,8.377777 1.05089,8.199997 L4.77592,4.488317 C4.86169,4.402547 4.92213,4.288247 4.92213,4.165077 L4.92213,4.163647 C4.92213,4.041497 4.86162,3.927197 4.77592,3.841427 L1.06098,0.133199 C0.97521,0.04729 0.85746,0.000135 0.73537,2.22044605e-16 C0.61322,2.22044605e-16 0.49709,0.04729 0.41139,0.133199 Z"/>
								</svg>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<div class="awsm-jobs-dw-empty"><?php esc_html_e( 'No active jobs found.', 'wp-job-openings' ); ?></div>
			<?php endif; ?>

			<!-- Footer Buttons -->
			<div class="awsm-jobs-dw-footer">
				<?php if ( current_user_can( 'edit_jobs' ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-overview' ) ); ?>" class="awsm-jobs-dw-btn awsm-jobs-dw-btn-primary">
						<?php esc_html_e( 'View More', 'wp-job-openings' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( current_user_can( 'edit_applications' ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="awsm-jobs-dw-btn awsm-jobs-dw-btn-secondary">
						<?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?>
					</a>
				<?php endif; ?>
			</div>

		</div><!-- .awsm-jobs-dw-wrap -->
		<?php
	}

	public static function get_active_jobs( $numberjobs = 5 ) {

		if ( ! class_exists( 'AWSM_Job_Openings_Overview' ) ) {
			return array();
		}
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
