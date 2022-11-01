<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Info {
	private static $instance = null;

	protected $cpath = null;

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );
		add_action( 'admin_init', array( $this, 'redirect_to_setup' ) );
		add_action( 'admin_head', array( $this, 'remove_menu' ) );
		add_action( 'in_admin_header', array( $this, 'nav_header' ) );
		add_action( 'admin_menu', array( $this, 'custom_admin_menu' ) );
		add_action( 'admin_footer', array( $this, 'admin_add_js' ) );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'empty_posts' ) );
		add_action( 'wp_ajax_awsm_jobs_setup', array( $this, 'handle_setup' ) );

		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function redirect_to_setup() {
		if ( ! get_transient( '_awsm_activation_redirect' ) ) {
			return;
		}
		delete_transient( '_awsm_activation_redirect' );
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'awsm-jobs-setup' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) );
		exit;
	}

	public function handle_setup() {
		$response = array(
			'success' => array(),
			'error'   => array(),
		);

		if ( ! isset( $_POST['awsm_job_nonce'] ) || ! wp_verify_nonce( $_POST['awsm_job_nonce'], 'awsm-jobs-setup' ) ) {
			$response['error'][] = esc_html__( 'Failed to verify nonce!', 'wp-job-openings' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['error'][] = esc_html__( 'You do not have sufficient permissions to make this request!', 'wp-job-openings' );
		}

		if ( count( $response['error'] ) === 0 ) {
			$update_options = array();
			$options        = array(
				'awsm_job_company_name'    => array(
					'label'       => __( 'Name of company', 'wp-job-openings' ),
					'sanitize_cb' => 'sanitize_text_field',
				),
				'awsm_hr_email_address'    => array(
					'label'       => __( 'Recruiter Email Address', 'wp-job-openings' ),
					'sanitize_cb' => 'sanitize_email',
				),
				'awsm_select_page_listing' => array(
					'label'       => __( 'Job listing page', 'wp-job-openings' ),
					'sanitize_cb' => 'intval',
				),
			);

			foreach ( $options as $option => $option_details ) {
				if ( ! isset( $_POST[ $option ] ) || empty( $_POST[ $option ] ) ) {
					/* translators: %s: Form field label */
					$response['error'][] = sprintf( esc_html__( '%s is required!', 'wp-job-openings' ), esc_html( $option_details['label'] ) );
				} else {
					$field_val = call_user_func( $option_details['sanitize_cb'], $_POST[ $option ] );
					if ( $option === 'awsm_hr_email_address' ) {
						if ( ! is_email( $field_val ) ) {
							$response['error'][] = esc_html__( 'Recruiter Email Address is invalid!', 'wp-job-openings' );
						}
					}
					if ( count( $response['error'] ) === 0 ) {
						$update_options[ $option ] = $field_val;
					}
				}
			}

			if ( count( $update_options ) === count( $options ) ) {
				foreach ( $update_options as $update_option => $field_val ) {
					update_option( $update_option, $field_val );
					if ( $update_option === 'awsm_hr_email_address' ) {
						update_option( 'awsm_jobs_hr_notification', $field_val );
						update_option( 'awsm_jobs_admin_to_notification', $field_val );
					} elseif ( $update_option === 'awsm_select_page_listing' ) {
						AWSM_Job_Openings::add_shortcode_to_page( $field_val );
					}
				}
				update_option( 'awsm_jobs_plugin_version', AWSM_JOBS_PLUGIN_VERSION );

				$response['redirect']  = esc_url_raw( add_query_arg( array( 'page' => 'awsm-jobs-overview' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) );
				$response['success'][] = esc_html__( 'Setup successfully completed!', 'wp-job-openings' );
			}
		}

		wp_send_json( $response );
	}

	public function custom_admin_menu() {
		add_submenu_page( 'edit.php?post_type=awsm_job_openings', esc_html__( 'WP Job Openings Setup', 'wp-job-openings' ), esc_html__( 'Setup', 'wp-job-openings' ), 'manage_options', 'awsm-jobs-setup', array( $this, 'setup_page' ) );
		add_submenu_page( 'edit.php?post_type=awsm_job_openings', esc_html__( 'Add-ons', 'wp-job-openings' ), esc_html__( 'Add-ons', 'wp-job-openings' ), 'manage_awsm_jobs', 'awsm-jobs-add-ons', array( $this, 'add_ons_page' ) );

		// Add Get PRO link in submenu.
		if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) && current_user_can( 'edit_others_applications' ) ) {
			global $submenu;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$submenu['edit.php?post_type=awsm_job_openings'][] = array(
				sprintf( '<span class="awsm-jobs-get-pro">%s</span>', esc_html__( 'Upgrade', 'wp-job-openings' ) ),
				'edit_others_applications',
				esc_url( 'https://awsm.in/get/wpjo-pro/' ),
			);
		}
	}

	public function remove_menu() {
		remove_submenu_page( 'edit.php?post_type=awsm_job_openings', 'awsm-jobs-setup' );
	}

	public function setup_page() {
		include_once $this->cpath . '/templates/info/setup.php';
	}

	public function add_ons_page() {
		include_once $this->cpath . '/templates/info/add-ons.php';
	}

	public function empty_posts( $which ) {
		global $post_type;

		if ( $post_type === 'awsm_job_openings' && $which === 'bottom' ) {
			$overview_data = AWSM_Job_Openings::get_overview_data();
			if ( $overview_data['total_jobs'] === 0 ) {
				$this->empty_jobs();
			}
		}
	}

	public static function empty_jobs() {
		$user_obj = wp_get_current_user();
		?>
			<div class="awsm-jobs-empty-list">
				<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/empty-state.svg' ); ?>" width="113" height="113" />
				<h2>
					<?php
						/* translators: %s: Current user name */
						printf( esc_html__( 'Welcome, %s', 'wp-job-openings' ), esc_html( $user_obj->display_name ) );
					?>
				</h2>
				<div class="awsm-jobs-empty-list-msg">
					<p><?php esc_html_e( 'Start adding job openings to your website', 'wp-job-openings' ); ?></p>
				</div>
				<div class="awsm-jobs-empty-list-btn-wrapper">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Add New Job Opening', 'wp-job-openings' ); ?></a>
				</div>
			</div>
		<?php
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
			$plugin_root      = WP_PLUGIN_DIR . '/' . $plugin_slug;
			$installed_plugin = file_exists( $plugin_root ) ? get_plugins( '/' . $plugin_slug ) : '';
		}
		if ( empty( $installed_plugin ) ) {
			if ( get_filesystem_method( array(), plugin_dir_path( dirname( dirname( __FILE__ ) ) ) ) === 'direct' && $add_on_details['type'] === 'free' ) {
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
				// Check if page is the setup page.
				if ( isset( $_GET['page'] ) && $_GET['page'] === 'awsm-jobs-setup' ) {
					$is_page = false;
				}
				// Check if the page have the block editor (Gutenberg) active.
				if ( method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
					$is_page = false;
				}
				// Check if the WPBakery Page Builder front-end editor is active or not.
				if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
					$is_page = false;
				}
			}
		}
		return $is_page;
	}

	public function admin_body_class( $classes ) {
		$nav_page = self::get_admin_nav_page();
		if ( ! empty( $nav_page ) ) {
			$classes .= ' awsm-job-admin-nav-page ';
			if ( $nav_page === 'edit-awsm_job_openings' ) {
				$overview_data = AWSM_Job_Openings::get_overview_data();
				if ( $overview_data['total_jobs'] === 0 ) {
					$classes .= 'awsm-jobs-empty-list-page ';
				}
			}
		}
		return $classes;
	}

	public function nav_header() {
		$nav_page = self::get_admin_nav_page();
		if ( ! empty( $nav_page ) ) :
			$nav_items = array(
				array(
					'visible' => current_user_can( 'edit_jobs' ),
					'id'      => 'edit-awsm_job_openings',
					'label'   => __( 'Openings', 'wp-job-openings' ),
					'url'     => admin_url( 'edit.php?post_type=awsm_job_openings' ),
				),
				array(
					'visible' => current_user_can( 'edit_applications' ),
					'id'      => 'edit-awsm_job_application',
					'label'   => __( 'Applications', 'wp-job-openings' ),
					'url'     => admin_url( 'edit.php?post_type=awsm_job_application' ),
				),
				array(
					'visible' => current_user_can( 'manage_awsm_jobs' ),
					'id'      => 'awsm_job_openings_page_awsm-jobs-settings',
					'label'   => __( 'Settings', 'wp-job-openings' ),
					'url'     => admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-settings' ),
				),
				array(
					'visible' => current_user_can( 'manage_awsm_jobs' ),
					'id'      => 'awsm_job_openings_page_awsm-jobs-add-ons',
					'label'   => __( 'Add-Ons', 'wp-job-openings' ),
					'url'     => admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-add-ons' ),
				),
				array(
					'visible' => ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) && current_user_can( 'edit_others_applications' ),
					'label'   => __( 'Upgrade', 'wp-job-openings' ),
					'url'     => 'https://awsm.in/get/wpjo-pro/',
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
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-overview' ) ); ?>">
							<?php esc_html_e( 'WP Job Openings', 'wp-job-openings' ); ?>
						</a>
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
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
