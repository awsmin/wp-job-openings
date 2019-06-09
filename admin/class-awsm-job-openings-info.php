<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Info {
	private static $instance = null;

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );
		add_action( 'admin_init', array( $this, 'welcome_page_redirect' ) );
		add_action( 'admin_head', array( $this, 'remove_menu' ) );
		add_action( 'admin_menu', array( $this, 'custom_admin_menu' ) );
		add_action( 'admin_footer', array( $this, 'admin_add_js' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function welcome_page_redirect() {
		if ( ! get_transient( '_awsm_activation_redirect' ) ) {
			return;
		}
		delete_transient( '_awsm_activation_redirect' );
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'awsm-jobs-welcome-page' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) );
		exit;
	}

	public function custom_admin_menu() {
		add_submenu_page( 'edit.php?post_type=awsm_job_openings', esc_html__( 'Welcome to Job Openings Plugin by Awsm.in', 'wp-job-openings' ), esc_html__( 'Getting started', 'wp-job-openings' ), 'manage_awsm_jobs', 'awsm-jobs-welcome-page', array( $this, 'welcome_page' ) );
		add_submenu_page( 'edit.php?post_type=awsm_job_openings', esc_html__( 'Help', 'wp-job-openings' ), esc_html__( 'Help', 'wp-job-openings' ), 'manage_awsm_jobs', 'awsm-jobs-help-page', array( $this, 'help_page' ) );
		add_submenu_page( 'edit.php?post_type=awsm_job_openings', esc_html__( 'Add-ons', 'wp-job-openings' ), esc_html__( 'Add-ons', 'wp-job-openings' ), 'manage_awsm_jobs', 'awsm-jobs-add-ons', array( $this, 'add_ons_page' ) );

		// Add Get PRO link in submenu.
		if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
			global $submenu;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$submenu['edit.php?post_type=awsm_job_openings'][] = array(
				sprintf( '<span class="awsm-jobs-get-pro" style="color: #00d1d4;">%s</span>', esc_html__( 'Get PRO', 'wp-job-openings' ) ),
				'manage_awsm_jobs',
				esc_url( 'https://1.envato.market/jjbEP' ),
			);
		}
	}

	public function remove_menu() {
		remove_submenu_page( 'edit.php?post_type=awsm_job_openings', 'awsm-jobs-welcome-page' );
		remove_submenu_page( 'edit.php?post_type=awsm_job_openings', 'awsm-jobs-help-page' );
	}

	public static function get_info_header( $page ) { ?>
		<div class="awsm-job-welcome">
			<div class="awsm-job-welcome-main">
				<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/job.png' ); ?>" alt="WP Job Openings">
				<div class="awsm-job-welcome-inner">
					<h1><?php esc_html_e( 'Welcome to Job Openings Plugin by Awsm.in', 'wp-job-openings' ); ?></h1>
					<p class="awsm-job-welcome-message"><?php esc_html_e( 'Thank you for trying WP Job Openings Plugin by AWSM Innovations. The plugin will help you setup the jobs page for in a few minutes. We encourage you to check out the plugin documentation and getting started guide below.', 'wp-job-openings' ); ?></p>
				</div><!-- .awsm-job-welcome-inner -->
			</div><!-- .awsm-job-welcome-main -->
			<h2 class="nav-tab-wrapper"><!-- nav-tab-active -->
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'awsm-jobs-welcome-page' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) ); ?>" class="nav-tab<?php echo ( $page === 'welcome' ) ? ' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Getting started', 'wp-job-openings' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'awsm-jobs-help-page' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) ); ?>" class="nav-tab<?php echo ( $page === 'help' ) ? ' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Help', 'wp-job-openings' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'awsm-jobs-add-ons' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) ); ?>" class="nav-tab<?php echo ( $page === 'add-ons' ) ? ' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Add-ons', 'wp-job-openings' ); ?></a>
			</h2>
		<?php
	}

	public static function get_info_footer() {
		?>
		</div><!-- .awsm-job-welcome -->
		<?php
	}

	public function welcome_page() {
		self::get_info_header( 'welcome' );
		include_once $this->cpath . '/templates/info/welcome.php';
		self::get_info_footer();
	}

	public function help_page() {
		self::get_info_header( 'help' );
		include_once $this->cpath . '/templates/info/help.php';
		self::get_info_footer();
	}

	public function add_ons_page() {
		self::get_info_header( 'add-ons' );
		include_once $this->cpath . '/templates/info/add-ons.php';
		self::get_info_footer();
	}

	public function get_add_on_btn_content( $plugin, $add_on_details = array() ) {
		$plugin_slug = $installed_plugin = $content = $btn_attrs = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
		$action_url  = $add_on_details['url'];
		$btn_action  = __( 'Get it now', 'wp-job-openings' );
		$btn_class   = 'button button-large';
		$btn_target  = '_self';
		if ( ! empty( $plugin ) ) {
			$plugin_arr       = explode( '/', esc_html( $plugin ) );
			$plugin_slug      = $plugin_arr[0];
			$installed_plugin = get_plugins( '/' . $plugin_slug );
		}
		if ( empty( $installed_plugin ) ) {
			if ( get_filesystem_method( array(), WP_PLUGIN_DIR ) === 'direct' && $add_on_details['type'] === 'free' ) {
				$btn_class .= ' install-now';
				$action_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
			} else {
				$btn_target = '_blank';
			}
		} else {
			if ( is_plugin_active( $plugin ) ) {
				$btn_action = __( 'Activated', 'wp-job-openings' );
				$action_url = '#';
				$btn_class .= ' button-disabled';
				$btn_attrs  = ' disabled';
			} else {
				$btn_action = __( 'Activate', 'wp-job-openings' );
				$action_url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin ), 'activate-plugin_' . $plugin );
				$btn_class .= ' activate-now';
			}
		}
		if ( ! empty( $action_url ) ) {
			$content = sprintf( '<a href="%2$s" class="%3$s" target="%4$s"%5$s>%1$s</a>', esc_html( $btn_action ), esc_url( $action_url ), esc_attr( $btn_class ), esc_attr( $btn_target ), esc_attr( $btn_attrs ) );
		}
		return $content;
	}

	public function admin_add_js() {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#adminmenu .awsm-jobs-get-pro').parent('a').attr('target', '_blank');
				});
			</script>
		<?php
	}
}
