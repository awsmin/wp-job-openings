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
		add_action( 'in_admin_header', array( $this, 'nav_header' ) );
		add_action( 'admin_menu', array( $this, 'custom_admin_menu' ) );
		add_action( 'admin_footer', array( $this, 'admin_add_js' ) );

		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
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

	public function welcome_page() {
		include_once $this->cpath . '/templates/info/welcome.php';
	}

	public function help_page() {
		include_once $this->cpath . '/templates/info/help.php';
	}

	public function add_ons_page() {
		include_once $this->cpath . '/templates/info/add-ons.php';
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

	public static function get_admin_nav_page() {
		$is_page = false;
		$screen  = get_current_screen();
		if ( ! empty( $screen ) ) {
			$post_type = $screen->post_type;
			if ( ( $post_type === 'awsm_job_openings' ) || ( $post_type === 'awsm_job_application' ) ) {
				$is_page = $screen->id;
				// Check if page is the welcome page.
				if ( isset( $_GET['page'] ) && $_GET['page'] === 'awsm-jobs-welcome-page' ) {
					$is_page = false;
				}
				// Check if the page have the block editor (Gutenberg) active.
				if ( method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
					$is_page = false;
				}
			}
		}
		return $is_page;
	}

	public function admin_body_class( $classes ) {
		$nav_page = self::get_admin_nav_page();
		if ( ! empty( $nav_page ) ) {
			$classes = ' awsm-job-admin-nav-page ';
		}
		return $classes;
	}

	public function nav_header() {
		$nav_page = self::get_admin_nav_page();
		if ( ! empty( $nav_page ) ) :
			$nav_items = array(
				array(
					'id'    => 'edit-awsm_job_openings',
					'label' => __( 'Openings', 'wp-job-openings' ),
					'url'   => admin_url( 'edit.php?post_type=awsm_job_openings' ),
				),
				array(
					'id'    => 'edit-awsm_job_application',
					'label' => __( 'Applications', 'wp-job-openings' ),
					'url'   => admin_url( 'edit.php?post_type=awsm_job_application' ),
				),
				array(
					'id'    => 'awsm_job_openings_page_awsm-jobs-settings',
					'label' => __( 'Settings', 'wp-job-openings' ),
					'url'   => admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-settings' ),
				),
				array(
					'id'    => 'awsm_job_openings_page_awsm-jobs-add-ons',
					'label' => __( 'Add-Ons', 'wp-job-openings' ),
					'url'   => admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-add-ons' ),
				),
				array(
					'visible' => ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ),
					'label'   => __( 'Get PRO', 'wp-job-openings' ),
					'url'     => 'https://1.envato.market/jjbEP',
					'class'   => array( 'button' ),
					'target'  => '_blank',
				),
			);
			/**
			 * Filters admin navigation items.
			 *
			 * @since 2.0.0
			 *
			 * @param array $nav_items Items data array.
			 * @param string $nav_page The page/screen ID.
			 */
			$nav_items = apply_filters( 'awsm_jobs_admin_nav_items', $nav_items, $nav_page );
			?>
				<div class="awsm-job-admin-nav-header">
					<div class="awsm-job-admin-nav-logo">
						<h1><?php esc_html_e( 'WP Job Openings', 'wp-job-openings' ); ?></h1>
					</div>
					<ul class="awsm-job-admin-nav">
						<?php
						foreach ( $nav_items as $nav_item ) {
							$display = isset( $nav_item['visible'] ) ? $nav_item['visible'] : true;
							if ( $display ) {
								$extra_atts = '';
								$class      = isset( $nav_item['class'] ) ? $nav_item['class'] : array();
								if ( isset( $nav_item['id'] ) && $nav_page === $nav_item['id'] ) {
									$class[] = 'active';
								}
								if ( ! empty( $class ) ) {
									$extra_atts = ' class="' . esc_attr( implode( ' ', $class ) ) . '"';
								}
								if ( isset( $nav_item['target'] ) ) {
									$extra_atts .= ' target="' . esc_attr( $nav_item['target'] ) . '"';
								}
								printf( '<li><a href="%2$s"%3$s>%1$s</a></li>', esc_html( $nav_item['label'] ), esc_url( $nav_item['url'] ), $extra_atts );
							}
						}
						?>
					</ul>
				</div>
			<?php
		endif;
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
