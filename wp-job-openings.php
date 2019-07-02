<?php
/**
 * WP Job Openings Plugin
 *
 * Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.
 *
 * @package wp-job-openings
 */

/**
 * Plugin Name: WP Job Openings
 * Plugin URI: https://wordpress.org/plugins/wp-job-openings/
 * Description: Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.
 * Author: AWSM Innovations
 * Author URI: https://awsm.in/
 * Version: 1.4.1
 * Licence: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text domain: wp-job-openings
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Constants
if ( ! defined( 'AWSM_JOBS_PLUGIN_BASENAME' ) ) {
	define( 'AWSM_JOBS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'AWSM_JOBS_PLUGIN_DIR' ) ) {
	define( 'AWSM_JOBS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( ! defined( 'AWSM_JOBS_PLUGIN_URL' ) ) {
	define( 'AWSM_JOBS_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}
if ( ! defined( 'AWSM_JOBS_PLUGIN_VERSION' ) ) {
	define( 'AWSM_JOBS_PLUGIN_VERSION', '1.4.1' );
}
if ( ! defined( 'AWSM_JOBS_UPLOAD_DIR_NAME' ) ) {
	define( 'AWSM_JOBS_UPLOAD_DIR_NAME', 'awsm-job-openings' );
}

// Helper functions
require_once AWSM_JOBS_PLUGIN_DIR . '/inc/helper-functions.php';

class AWSM_Job_Openings {
	private static $instance = null;

	public function __construct() {
		// Require Classes
		self::load_classes();
		// Initialize Classes
		$this->awsm_core = AWSM_Job_Openings_Core::init();
		$this->awsm_form = AWSM_Job_Openings_Form::init();
		AWSM_Job_Openings_Filters::init();
		if ( is_admin() ) {
			AWSM_Job_Openings_Meta::init();
			AWSM_Job_Openings_Settings::init( $this->awsm_core );
			AWSM_Job_Openings_Info::init();
		}

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'template_functions' ) );
		add_action( 'init', array( $this, 'init_actions' ) );
		add_action( 'wp', array( $this, 'awsm_openings_cron_job' ) );
		add_action( 'wp_head', array( $this, 'awsm_wp_head' ) );
		add_action( 'awsm_check_for_expired_jobs', array( $this, 'check_date_and_change_status' ) );
		add_action( 'wp_loaded', array( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'awsm_enqueue_scripts' ) );
		add_action( 'template_redirect', array( $this, 'redirect_attachment_page' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'wp_ajax_awsm_view_count', array( $this, 'job_views_handler' ) );
		add_action( 'wp_ajax_nopriv_awsm_view_count', array( $this, 'job_views_handler' ) );
		add_action( 'wp_footer', array( $this, 'display_structured_data' ) );
		$this->admin_actions();

		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'the_content', array( $this, 'awsm_jobs_content' ), 100 );
		add_filter( 'single_template', array( $this, 'jobs_single_template' ) );
		add_filter( 'archive_template', array( $this, 'jobs_archive_template' ) );
		$this->admin_filters();
		add_shortcode( 'awsmjobs', array( $this, 'awsm_jobs_shortcode' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public static function load_classes() {
		$prefix  = 'class-awsm-job-openings';
		$classes = array( 'core', 'filters', 'form' );
		foreach ( $classes as $class ) {
			require_once AWSM_JOBS_PLUGIN_DIR . "/inc/{$prefix}-{$class}.php";
		}
		if ( is_admin() ) {
			$classes = array( 'meta', 'settings', 'info' );
			foreach ( $classes as $class ) {
				require_once AWSM_JOBS_PLUGIN_DIR . "/admin/{$prefix}-{$class}.php";
			}
		}
	}

	public function activate() {
		$this->register_default_settings();
		$this->awsm_core->register();
		$this->create_page_when_activate();
		flush_rewrite_rules();
		$this->activate_welcome_page();
	}

	public function deactivate() {
		$this->clear_transients();
		$this->clear_cron_jobs();
		$this->awsm_core->unregister();
		flush_rewrite_rules();
	}

	private function register_default_settings() {
		if ( ! class_exists( 'AWSM_Job_Openings_Settings' ) ) {
			require_once AWSM_JOBS_PLUGIN_DIR . '/admin/class-awsm-job-openings-settings.php';
		}
		AWSM_Job_Openings_Settings::register_defaults();
	}

	public function activate_welcome_page() {
		set_transient( '_awsm_activation_redirect', true, MINUTE_IN_SECONDS );
	}

	private function clear_transients() {
		delete_transient( '_awsm_activation_redirect' );
		delete_transient( '_awsm_add_ons_data' );
	}

	public function create_page_when_activate() {
		$default_page_id = get_option( 'awsm_jobs_default_listing_page_id' );
		if ( empty( $default_page_id ) ) {
			$user    = get_current_user_id();
			$post    = array(
				'post_author'  => $user,
				'post_name'    => 'job-openings',
				'post_status'  => 'publish',
				'post_content' => '<p>[awsmjobs]</p>',
				'post_title'   => esc_html__( 'Jobs', 'wp-job-openings' ),
				'post_type'    => 'page',
			);
			$post_id = wp_insert_post( $post );
			if ( ! empty( $post_id ) && ! is_wp_error( $post_id ) ) {
				update_option( 'awsm_jobs_default_listing_page_id', $post_id );
			}
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'wp-job-openings', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function template_functions() {
		include_once AWSM_JOBS_PLUGIN_DIR . '/inc/template-functions.php';
	}

	public function init_actions() {
		$this->unregister_awsm_jobs_taxonomies();
		$this->awsm_jobs_taxonomies();
		$this->awsm_custom_expired_status();
	}

	public function admin_actions() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'awsm_admin_enqueue_scripts' ) );
			add_action( 'admin_head', array( $this, 'admin_head_actions' ) );
			add_action( 'edit_form_top', array( $this, 'awsm_admin_single_subtitle' ) );
			add_action( 'save_post', array( $this, 'awsm_job_save_post' ), 100, 2 );
			add_action( 'before_delete_post', array( $this, 'delete_attachment_post' ) );
			add_action( 'restrict_manage_posts', array( $this, 'awsm_admin_filtering_posts' ) );
			add_action( 'before_awsm_job_settings_init', array( $this, 'no_script_msg' ) );
		}
	}

	public function admin_head_actions() {
		$this->awsm_admin_single_title();
		$this->awsm_job_application_screen_layout();
	}

	public function admin_filters() {
		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . AWSM_JOBS_PLUGIN_BASENAME, array( $this, 'awsm_quick_settings' ) );
			add_filter( 'manage_awsm_job_openings_posts_columns', array( $this, 'awsm_job_custom_column_member' ) );
			add_filter( 'manage_awsm_job_openings_posts_custom_column', array( $this, 'awsm_job_custom_column_member_data' ), 10, 2 );
			add_filter( 'manage_awsm_job_application_posts_columns', array( $this, 'awsm_job_application_manage' ) );
			add_filter( 'manage_awsm_job_application_posts_custom_column', array( $this, 'awsm_job_application_manage_custom_data' ), 10, 2 );
			add_filter( 'parse_query', array( $this, 'awsm_admin_filter_posts' ) );
			add_filter( 'months_dropdown_results', array( $this, 'awsm_job_month_dropdown' ), 10, 2 );
			add_filter( 'views_edit-awsm_job_openings', array( $this, 'modified_post_status_filter' ) );
			add_filter( 'views_edit-awsm_job_application', array( $this, 'awsm_job_application_action_links' ) );
			add_filter( 'bulk_actions-edit-awsm_job_application', array( $this, 'awsm_job_application_bulk_actions' ) );
			add_filter( 'post_row_actions', array( $this, 'awsm_posts_row_actions' ), 10, 2 );
		}
	}

	public function awsm_jobs_shortcode( $atts ) {
		if ( ! function_exists( 'awsm_jobs_query' ) ) {
			return;
		}
		$shortcode_atts = shortcode_atts(
			array(
				'filters'  => get_option( 'awsm_enable_job_filter_listing' ) !== 'enabled' ? 'no' : 'yes',
				'listings' => get_option( 'awsm_jobs_list_per_page' ),
				'loadmore' => 'yes',
			),
			$atts,
			'awsmjobs'
		);
		ob_start();
		include self::get_template_path( 'job-openings-view.php' );
		return ob_get_clean();
	}

	public function register_widgets() {
		$widgets = array( 'recent-jobs' );
		foreach ( $widgets as $widget ) {
			include_once AWSM_JOBS_PLUGIN_DIR . "/inc/widgets/class-awsm-job-openings-{$widget}-widget.php";
		}
	}

	public function awsm_quick_settings( $links ) {
		$links[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-settings' ) ), esc_html__( 'Settings', 'wp-job-openings' ) );
		return $links;
	}

	public function awsm_job_custom_column_member( $columns ) {
		$columns = array(
			'cb'                    => '<input type="checkbox" />',
			'title'                 => esc_attr__( 'Job Title', 'wp-job-openings' ),
			'job_id'                => esc_attr__( 'Job ID', 'wp-job-openings' ),
			'awsm_job_applications' => esc_attr__( 'Applications', 'wp-job-openings' ),
			'awsm_job_expiry'       => esc_attr__( 'Expiry', 'wp-job-openings' ),
			'awsm_job_post_views'   => esc_attr__( 'Views', 'wp-job-openings' ),
			'awsm_job_conversion'   => esc_attr__( 'Conversion', 'wp-job-openings' ),
		);
		return $columns;
	}

	public static function get_applications( $job_id ) {
		$applications = get_children(
			array(
				'post_parent' => $job_id,
				'post_type'   => 'awsm_job_application',
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		return $applications;
	}

	public function awsm_job_custom_column_member_data( $column, $post_id ) {
		$application_count = count( self::get_applications( $post_id ) );
		$job_views         = get_post_meta( $post_id, 'awsm_views_count', true );
		$default_display   = '<span aria-hidden="true">—</span>';

		switch ( $column ) {
			case 'job_id':
					edit_post_link( esc_html( $post_id ) );
				break;

			case 'awsm_job_applications':
					$output = $default_display;
				if ( $application_count > 0 ) {
					$output = sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $post_id ) ), $application_count );
				}
					echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'awsm_job_expiry':
					$expiry_on_list = get_post_meta( $post_id, 'awsm_set_exp_list', true );
					$job_expiry     = get_post_meta( $post_id, 'awsm_job_expiry', true );
					echo ( $expiry_on_list === 'set_listing' && ! empty( $job_expiry ) ) ? esc_html( date_i18n( __( 'M j, Y', 'default' ), strtotime( $job_expiry ) ) ) : $default_display; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'awsm_job_post_views':
					echo ( ! empty( $job_views ) ) ? esc_html( $job_views ) : $default_display; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'awsm_job_conversion':
					$output = $default_display;
				if ( $job_views > 0 ) {
					$conversion_rate = ( $application_count / $job_views ) * 100;
					$output          = round( $conversion_rate, 2 ) . '%';
				}
					echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
		}
	}

	public function awsm_job_application_manage( $columns ) {
		$columns = array(
			'cb'               => '<input type="checkbox" />',
			'awsm-photo'       => '',
			'title'            => esc_attr__( 'Applicant', 'wp-job-openings' ),
			'application_id'   => esc_attr__( 'ID', 'wp-job-openings' ),
			'applied_for'      => esc_attr__( 'Job', 'wp-job-openings' ),
			'submisssion_time' => esc_attr__( 'Applied on', 'wp-job-openings' ),
		);
		return $columns;
	}

	public function awsm_job_application_manage_custom_data( $columns, $post_id ) {
		global $submission;
		$job_id   = get_post_meta( $post_id, 'awsm_job_id', true );
		$job_name = get_post_meta( $post_id, 'awsm_apply_for', true );
		switch ( $columns ) {
			case 'awsm-photo':
				$applicant_email = esc_attr( get_post_meta( $post_id, 'awsm_applicant_email', true ) );
				$avatar          = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 32 ) );
				echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'application_id':
				edit_post_link( esc_html( $post_id ) );
				break;

			case 'applied_for':
				printf( '<a href="%2$s" title="%3$s">%1$s</a>', esc_html( $job_name ), esc_url( get_edit_post_link( $job_id ) ), esc_attr( __( 'View Job: ', 'wp-job-openings' ) . $job_name ) );
				break;

			case 'submisssion_time':
				$submission = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wp-job-openings' );
				echo esc_html( $submission );
				break;
		}
	}

	public function awsm_custom_expired_status() {
		register_post_status(
			'expired',
			array(
				'label'                     => esc_attr__( 'Expired', 'wp-job-openings' ),
				'public'                    => true,
				'protected'                 => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: posts count with expired status */
				'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'wp-job-openings' ),
			)
		);
	}

	public function awsm_openings_cron_job() {
		if ( ! wp_next_scheduled( 'awsm_check_for_expired_jobs' ) ) {
			wp_schedule_event( time(), 'hourly', 'awsm_check_for_expired_jobs' );
		}
	}

	public function clear_cron_jobs() {
		wp_clear_scheduled_hook( 'awsm_check_for_expired_jobs' );
	}

	public function check_date_and_change_status() {
		$args  = array(
			'post_type'      => 'awsm_job_openings',
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => 'awsm_set_exp_list',
					'value' => 'set_listing',
				),
				array(
					'key'     => 'awsm_job_expiry',
					'value'   => date( 'Y-m-d' ),
					'type'    => 'DATE',
					'compare' => '<',
				),
			),
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				// still doing some usual checking even if meta query is used!
				$expiry_on_list  = get_post_meta( get_the_ID(), 'awsm_set_exp_list', true );
				$expiration_date = get_post_meta( get_the_ID(), 'awsm_job_expiry', true );
				if ( $expiry_on_list === 'set_listing' && ! empty( $expiration_date ) ) {
					$expiration_date_time = strtotime( $expiration_date );
					if ( $expiration_date_time < ( time() - ( 24 * 60 * 60 ) ) ) {
						$jobs                = array();
						$jobs['ID']          = get_the_ID();
						$jobs['post_status'] = 'expired';
						wp_update_post( $jobs );
					}
				}
			}
		}
	}

	public function modified_post_status_filter( $views ) {
		if ( isset( $views['publish'] ) ) {
			$views['publish'] = str_replace( esc_html__( 'Published', 'wp-job-openings' ), esc_html__( 'Current Openings', 'wp-job-openings' ), $views['publish'] );
		}
		if ( isset( $views['expired'] ) ) {
			$views['expired'] = str_replace( esc_html__( 'Expired', 'wp-job-openings' ), esc_html__( 'Inactive', 'wp-job-openings' ), $views['expired'] );
		}
		return $views;
	}

	public function awsm_admin_filtering_posts() {
		global $typenow;
		if ( $typenow === 'awsm_job_application' ) {
			$jobs_post_filter = '';
			if ( isset( $_GET['awsm_filter_posts'] ) ) {
				$jobs_post_filter = intval( $_GET['awsm_filter_posts'] );
			}
			$custom_posts = array(
				'posts_per_page' => -1,
				'post_type'      => 'awsm_job_openings',
				'post_status'    => array( 'publish', 'expired' ),
			);
			$job_posts    = get_posts( $custom_posts );

			echo "<select name='awsm_filter_posts'>";
				echo "<option value=''>" . esc_html__( 'All Jobs', 'wp-job-openings' ) . '</option>';
			foreach ( $job_posts as $jobs ) {
				$selected   = '';
				$post_id    = $jobs->ID;
				$post_title = $jobs->post_title;
				if ( $jobs_post_filter === $post_id ) {
					$selected = ' selected';
				}
				printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $post_id ), esc_html( $post_title ), esc_attr( $selected ) );
			}
			echo '</select>';
		}
	}

	public function awsm_admin_filter_posts( $query ) {
		global $pagenow;
		$type = 'awsm_job_application';
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
		}
		if ( $type === 'awsm_job_application' && is_admin() && $pagenow === 'edit.php' && isset( $_GET['awsm_filter_posts'] ) && $query->is_main_query() ) {
			$meta_value = intval( $_GET['awsm_filter_posts'] );
			if ( $meta_value ) {
				$query->query_vars['meta_key']   = 'awsm_job_id';
				$query->query_vars['meta_value'] = $meta_value;
			}
		}
	}

	public function awsm_job_month_dropdown( $months, $post_type ) {
		if ( $post_type === 'awsm_job_openings' || $post_type === 'awsm_job_application' ) {
			$months = array();
		}
		return $months;
	}

	public function awsm_wp_head() {
		global $post;
		if ( is_singular( 'awsm_job_openings' ) ) {
			// block search engine robots to expired jobs
			if ( function_exists( 'wp_no_robots' ) && $post->post_status === 'expired' && get_option( 'awsm_jobs_expired_jobs_block_search' ) === 'block_expired' ) {
				wp_no_robots();
			}
		}
	}

	public function job_views_handler() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['awsm_job_id'] ) ) {
			$post_id = intval( $_POST['awsm_job_id'] );
			if ( $post_id && get_post_type( $post_id ) === 'awsm_job_openings' ) {
				$count           = 1;
				$post_view_count = get_post_meta( $post_id, 'awsm_views_count', true );
				if ( ! empty( $post_view_count ) ) {
					$count = $post_view_count + 1;
				}
				update_post_meta( $post_id, 'awsm_views_count', $count );
			}
		}
		wp_die();
	}

	public function no_script_msg() { ?>
		<noscript>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'JavaScript is required! Please enable it in your browser.', 'wp-job-openings' ); ?></p>
			</div>
		</noscript>
		<?php
	}

	public function register_scripts() {
		wp_register_style( 'awsm-jobs-general', AWSM_JOBS_PLUGIN_URL . '/assets/css/general.min.css', false, AWSM_JOBS_PLUGIN_VERSION, 'all' );
	}

	public function awsm_enqueue_scripts() {
		wp_enqueue_style( 'awsm-jobs-general' );
		wp_enqueue_style( 'awsm-jobs-style', AWSM_JOBS_PLUGIN_URL . '/assets/css/style.min.css', array( 'awsm-jobs-general' ), AWSM_JOBS_PLUGIN_VERSION, 'all' );

		$is_recaptcha_set = $this->awsm_form->is_recaptcha_set();
		if ( is_singular( 'awsm_job_openings' ) && $is_recaptcha_set ) {
			wp_enqueue_script( 'g-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '2.0', false );
		}
		wp_enqueue_script( 'awsm-job-scripts', AWSM_JOBS_PLUGIN_URL . '/assets/js/script.min.js', array( 'jquery' ), AWSM_JOBS_PLUGIN_VERSION, true );
		global $post;
		wp_localize_script(
			'awsm-job-scripts',
			'awsmJobsPublic',
			array(
				'ajaxurl'            => admin_url( 'admin-ajax.php' ),
				'is_tax_archive'     => is_tax(),
				'job_id'             => is_singular( 'awsm_job_openings' ) ? $post->ID : 0,
				'wp_max_upload_size' => ( wp_max_upload_size() ) ? ( wp_max_upload_size() ) : 0,
				'i18n'               => array(
					'loading_text'   => esc_html__( 'Loading...', 'wp-job-openings' ),
					'form_error_msg' => array(
						'general'         => esc_html__( 'Error in submitting your application. Please try again later!', 'wp-job-openings' ),
						'file_validation' => esc_html__( 'The file you have selected is too large.', 'wp-job-openings' ),
					),
				),
			)
		);
	}

	public function awsm_admin_enqueue_scripts() {
		$screen = get_current_screen();
		wp_register_style( 'awsm-job-admin', AWSM_JOBS_PLUGIN_URL . '/assets/css/admin.min.css', array( 'awsm-jobs-general' ), AWSM_JOBS_PLUGIN_VERSION, 'all' );

		wp_register_script( 'awsm-job-admin', AWSM_JOBS_PLUGIN_URL . '/assets/js/admin.min.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-util' ), AWSM_JOBS_PLUGIN_VERSION, true );

		if ( ! empty( $screen ) ) {
			$post_type = $screen->post_type;
			if ( ( $post_type === 'awsm_job_openings' ) || ( $post_type === 'awsm_job_application' ) ) {
				wp_enqueue_style( 'awsm-jobs-general' );
				wp_enqueue_style( 'awsm-job-admin' );
				wp_enqueue_script( 'awsm-job-admin' );
			}
		}

		wp_localize_script(
			'awsm-job-admin',
			'awsmJobsAdmin',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'plugin_url' => AWSM_JOBS_PLUGIN_URL,
				'nonce'      => wp_create_nonce( 'awsm-admin-nonce' ),
				'i18n'       => array(
					'select2_no_page' => esc_html__( 'Select a page', 'wp-job-openings' ),
				),
			)
		);
	}

	public static function get_template_path( $template_name, $sub_dir_name = false ) {
		$path        = $rel_path = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
		$plugin_base = 'wp-job-openings';
		if ( ! empty( $sub_dir_name ) ) {
			$rel_path .= "/{$sub_dir_name}";
		}
		$rel_path      .= "/{$template_name}";
		$theme_base_dir = trailingslashit( get_stylesheet_directory() );
		if ( file_exists( $theme_base_dir . $plugin_base . $rel_path ) ) {
			$path = $theme_base_dir . $plugin_base . $rel_path;
		} else {
			$path = AWSM_JOBS_PLUGIN_DIR . '/inc/templates' . $rel_path;
		}
		return $path;
	}

	public function body_classes( $classes ) {
		global $wp_query;
		if ( is_page() ) {
			$job_listing_page_id = get_option( 'awsm_select_page_listing', get_option( 'awsm_jobs_default_listing_page_id' ) );
			$current_page_id     = $wp_query->get_queried_object_id();
			if ( $current_page_id == $job_listing_page_id ) {
				$classes[] = 'listing-page-awsm_job_openings';
			}
		}
		if ( is_singular( 'awsm_job_openings' ) ) {
			$job_details_template = get_option( 'awsm_jobs_details_page_template', 'default' );
			if ( $job_details_template === 'custom' ) {
				$key = array_search( 'awsm_job_openings-template-default', $classes );
				if ( $key !== false ) {
					$classes[ $key ] = 'awsm_job_openings-template-custom';
				}
			}
		}
		return $classes;
	}

	public function awsm_jobs_content( $content ) {
		if ( ! is_singular( 'awsm_job_openings' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		ob_start();
		include_once self::get_template_path( 'job-content.php' );
		return ob_get_clean();
	}

	public function jobs_single_template( $single_template ) {
		global $post;
		$job_details_template = get_option( 'awsm_jobs_details_page_template', 'default' );
		if ( is_object( $post ) && $post->post_type === 'awsm_job_openings' && $job_details_template === 'custom' ) {
			$single_template = self::get_template_path( 'single-job.php' );
		}
		return $single_template;
	}

	public function jobs_archive_template( $archive_template ) {
		global $post;
		if ( is_object( $post ) && $post->post_type === 'awsm_job_openings' ) {
			$archive_template = self::get_template_path( 'archive-job.php' );
		}
		return $archive_template;
	}

	public function awsm_admin_single_title() {
		global $post, $title, $action, $current_screen;
		if ( isset( $current_screen->post_type ) && $current_screen->post_type === 'awsm_job_application' && $action === 'edit' ) {
			/* translators: %1$s: application id, %2$s: job title */
			$title = esc_html( sprintf( __( 'Application #%1$s for %2$s', 'wp-job-openings' ), $post->ID, get_post_meta( $post->ID, 'awsm_apply_for', true ) ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	public function awsm_admin_single_subtitle( $post ) {
		global $action;
		if ( $post->post_type === 'awsm_job_application' && $action === 'edit' ) {
			/* translators: %s: application submission time */
			$submitted_date = sprintf( __( 'Submitted on %s', 'wp-job-openings' ), date_i18n( __( 'g:ia, j F Y', 'default' ), strtotime( $post->post_date ) ) );
			$subtitle       = '<span class="awsm-application-submission-date">' . esc_html( $submitted_date ) . '</span>';
			$user_ip        = get_post_meta( $post->ID, 'awsm_applicant_ip', true );
			if ( ! empty( $user_ip ) ) {
				$subtitle .= ' <span class="awsm-applicant-ip">' . esc_html( __( 'from IP ', 'wp-job-openings' ) . $user_ip ) . '</span>';
			}
			echo $subtitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function awsm_jobs_taxonomies() {
		$awsm_filters = get_option( 'awsm_jobs_filter' );
		if ( ! empty( $awsm_filters ) ) {
			foreach ( $awsm_filters as $awsm_filter ) {
				if ( isset( $awsm_filter['taxonomy'], $awsm_filter['filter'] ) ) {
					$taxonomy   = $awsm_filter['taxonomy'];
					$tax_length = strlen( $taxonomy );
					if ( ! taxonomy_exists( $taxonomy ) && ( $tax_length > 0 && $tax_length <= 32 ) ) {
						$args = array(
							'labels'       => array( 'name' => esc_html( $awsm_filter['filter'] ) ),
							'show_ui'      => false,
							'show_in_menu' => false,
							'query_var'    => true,
							'rewrite'      => array( 'slug' => $taxonomy ),
						);
						register_taxonomy( $taxonomy, array( 'awsm_job_openings' ), $args );
					}
					if ( isset( $awsm_filter['tags'] ) ) {
						if ( ! empty( $awsm_filter['tags'] ) ) {
							foreach ( $awsm_filter['tags'] as $filter_tag ) {
								$slug = sanitize_title( $filter_tag );
								if ( ! get_term_by( 'slug', $slug, $taxonomy ) ) {
									$args = array( 'slug' => $slug );
									wp_insert_term( $filter_tag, $taxonomy, $args );
								}
							}
						}
					}
				}
			}
		}
	}

	public function unregister_awsm_jobs_taxonomies() {
		$remove_filters = get_option( 'awsm_jobs_remove_filters' );
		if ( ! empty( $remove_filters ) ) {
			foreach ( $remove_filters as $filter ) {
				if ( taxonomy_exists( $filter ) ) {
					unregister_taxonomy_for_object_type( $filter, 'awsm_job_openings' );
				}
			}
			update_option( 'awsm_jobs_remove_filters', '' );
		}
	}

	public function sanitize_term( $term ) {
		if ( is_numeric( $term ) ) {
			$term = intval( $term );
		} else {
			$term = sanitize_text_field( $term );
		}
		return $term;
	}

	public function awsm_job_save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['awsm_jobs_posts_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['awsm_jobs_posts_nonce'], 'awsm_save_post_meta' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( $post->post_type === 'awsm_job_openings' ) {
			// handle job specifications.
			if ( isset( $_POST['awsm_job_spec_terms'] ) ) {
				$specs = $_POST['awsm_job_spec_terms'];
				if ( ! empty( $specs ) ) {
					foreach ( $specs as $taxonomy => $spec_terms ) {
						if ( taxonomy_exists( $taxonomy ) ) {
							$terms = array_map( array( $this, 'sanitize_term' ), $spec_terms );
							wp_set_object_terms( $post_id, $terms, $taxonomy, false );
						}
					}
				}
			}

			// handle job expiry.
			$expiry_on_list  = isset( $_POST['awsm_set_exp_list'] ) ? sanitize_text_field( $_POST['awsm_set_exp_list'] ) : '';
			$awsm_job_expiry = isset( $_POST['awsm_job_expiry'] ) ? sanitize_text_field( $_POST['awsm_job_expiry'] ) : '';
			$display_list    = isset( $_POST['awsm_exp_list_display'] ) ? sanitize_text_field( $_POST['awsm_exp_list_display'] ) : '';
			$job_expiry_meta = array(
				'awsm_set_exp_list'     => $expiry_on_list,
				'awsm_job_expiry'       => $awsm_job_expiry,
				'awsm_exp_list_display' => $display_list,
			);
			foreach ( $job_expiry_meta as $meta_key => $meta_value ) {
				$olddata = get_post_meta( $post_id, $meta_key, true );
				if ( ! empty( $meta_value ) ) {
					if ( $meta_value !== $olddata && $expiry_on_list === 'set_listing' ) {
						update_post_meta( $post_id, $meta_key, $meta_value );
					} elseif ( empty( $expiry_on_list ) ) {
						delete_post_meta( $post_id, $meta_key, $meta_value );
					}
				} else {
					delete_post_meta( $post_id, $meta_key, $olddata );
				}
			}
			if ( $expiry_on_list === 'set_listing' && ! empty( $awsm_job_expiry ) ) {
				$expiration_time = strtotime( $awsm_job_expiry );
				if ( $expiration_time < ( time() - ( 24 * 60 * 60 ) ) ) {
					$post_data                = array();
					$post_data['ID']          = $post_id;
					$post_data['post_status'] = 'expired';
					// unhook this function so it doesn't loop infinitely
					remove_action( 'save_post', array( $this, 'awsm_job_save_post' ), 100, 2 );
					wp_update_post( $post_data );
					// now, re-hook this function
					add_action( 'save_post', array( $this, 'awsm_job_save_post' ), 100, 2 );
				}
			}
		}
	}

	public function delete_attachment_post( $post_id ) {
		if ( get_post_type( $post_id ) === 'awsm_job_application' ) {
			$attachment_id = get_post_meta( $post_id, 'awsm_attachment_id', true );
			if ( ! empty( $attachment_id ) ) {
				wp_delete_attachment( $attachment_id );
			}
		}
	}

	public function redirect_attachment_page() {
		if ( is_attachment() ) {
			global $post;
			$post_parent = $post->post_parent;
			if ( ! empty( $post_parent ) && get_post_type( $post_parent ) === 'awsm_job_application' ) {
				wp_redirect( esc_url( home_url( '/' ) ), 301 );
				exit;
			}
		}
	}

	public function awsm_job_application_action_links( $views ) {
		$remove_views = array( 'publish', 'mine', 'future', 'sticky', 'draft', 'pending' );
		foreach ( $remove_views as $view ) {
			if ( isset( $views[ $view ] ) ) {
				unset( $views[ $view ] );
			}
		}
		return $views;
	}

	public function awsm_job_application_bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	public function awsm_posts_row_actions( $actions, $post ) {
		if ( $post->post_type === 'awsm_job_openings' ) {
			$actions['view_applications'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $post->ID ) ), esc_html__( 'View Applications', 'wp-job-openings' ) );
		}
		if ( $post->post_type === 'awsm_job_application' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	public function awsm_job_application_screen_layout() {
		$screen = get_current_screen();
		if ( ! empty( $screen ) ) {
			if ( $screen->base === 'post' && $screen->post_type === 'awsm_job_application' && $screen->id === 'awsm_job_application' ) {
				add_screen_option(
					'layout_columns',
					apply_filters(
						'awsm_job_application_screen_layout_options',
						array(
							'default' => 2,
							'max'     => 2,
						)
					)
				);
			}
		}
	}

	public static function get_listings_per_page( $shortcode_atts ) {
		return ( isset( $shortcode_atts['listings'] ) && is_numeric( $shortcode_atts['listings'] ) && $shortcode_atts['listings'] > 0 ) ? intval( $shortcode_atts['listings'] ) : get_option( 'awsm_jobs_list_per_page' );
	}

	public static function awsm_job_query_args( $filters = array(), $shortcode_atts = array() ) {
		$args = array();
		if ( is_tax() ) {
			$q_obj    = get_queried_object();
			$taxonomy = $q_obj->taxonomy;
			$term_id  = $q_obj->term_id;
			$filters  = array( $taxonomy => $term_id );
		}
		if ( ! empty( $filters ) ) {
			foreach ( $filters as $taxonomy => $term_id ) {
				if ( ! empty( $term_id ) ) {
					$spec                = array(
						'taxonomy' => $taxonomy,
						'field'    => 'id',
						'terms'    => $term_id,
					);
					$args['tax_query'][] = $spec;
				}
			}
		}

		$list_per_page          = self::get_listings_per_page( $shortcode_atts );
		$hide_expired_jobs      = get_option( 'awsm_jobs_expired_jobs_listings' );
		$args['post_type']      = 'awsm_job_openings';
		$args['posts_per_page'] = $list_per_page;
		if ( $hide_expired_jobs === 'expired' ) {
			if ( $list_per_page > 0 ) {
				$args['post_status'] = array( 'publish' );
			} else {
				$args['numberposts'] = -1;
			}
		} else {
			$args['post_status'] = array( 'publish', 'expired' );
		}

		/**
		 * Filters the arguments for the jobs query.
		 *
		 * @since 1.4
		 *
		 * @param array $args arguments.
		 * @param array $filters Applicable filters.
		 * @param array $shortcode_atts Shortcode attributes.
		 */
		return apply_filters( 'awsm_job_query_args', $args, $filters, $shortcode_atts );
	}

	public static function get_job_listing_view() {
		$view    = 'list';
		$options = get_option( 'awsm_jobs_listing_view' );
		if ( $options === 'grid-view' ) {
			$view = 'grid';
		}
		return $view;
	}

	public static function get_job_listing_view_class() {
		$view       = self::get_job_listing_view();
		$view_class = 'awsm-lists';
		if ( $view === 'grid' ) {
			$number_columns = get_option( 'awsm_jobs_number_of_columns' );
			$view_class     = 'awsm-row';
			$column_class   = 'awsm-grid-col-' . $number_columns;
			if ( $number_columns == 1 ) {
				$column_class = 'awsm-grid-col';
			}
			$view_class .= ' ' . $column_class;
		}
		$view_class = apply_filters( 'awsm_job_listing_view_class', $view_class );
		return sprintf( 'awsm-job-listings %s', $view_class );
	}

	public static function get_job_listing_data_attrs( $shortcode_atts = array() ) {
		$attrs             = array();
		$attrs['listings'] = self::get_listings_per_page( $shortcode_atts );
		if ( is_tax() ) {
			$q_obj             = get_queried_object();
			$attrs['taxonomy'] = $q_obj->taxonomy;
			$attrs['term-id']  = $q_obj->term_id;
		}
		return apply_filters( 'awsm_job_listing_data_attrs', $attrs );
	}

	public static function get_job_details_class() {
		$column_class       = '';
		$job_details_layout = get_option( 'awsm_jobs_details_page_layout' );
		if ( $job_details_layout === 'two' ) {
			$column_class = ' awsm-col-2';
		}
		return apply_filters( 'awsm_job_details_class', $column_class );
	}

	public static function get_job_expiry_details( $post_id, $post_status ) {
		$content         = '';
		$display         = false;
		$expiry_date     = get_post_meta( $post_id, 'awsm_job_expiry', true );
		$display_in_list = get_post_meta( $post_id, 'awsm_exp_list_display', true );
		if ( $display_in_list === 'list_display' && ( get_option( 'awsm_jobs_hide_expiry_date' ) !== 'hide_date' ) ) {
			$display = true;
		}
		if ( ! empty( $expiry_date ) && $display ) {
			$display_status = esc_html__( 'Closing on', 'wp-job-openings' );
			if ( $post_status === 'expired' ) {
				$display_status = esc_html__( 'Expired on', 'wp-job-openings' );
			}
			$content = sprintf( '<div class="awsm-job-expiry-details"><span class="awsm-job-expiration-label">%1$s:</span> <span class="awsm-job-expiration-content">%2$s</span></div>', esc_html( $display_status ), esc_html( date_i18n( __( 'M j, Y', 'default' ), strtotime( $expiry_date ) ) ) );
		}
		return apply_filters( 'awsm_job_expiry_details_content', $content );
	}

	public static function get_specifications_content( $post_id, $display_label, $filter_data = array(), $listing_specs = array() ) {
		$spec_content = '';
		$filter_data  = ! empty( $filter_data ) ? $filter_data : get_option( 'awsm_jobs_filter' );
		if ( ! empty( $filter_data ) ) {
			$spec_keys          = wp_list_pluck( $filter_data, 'taxonomy' );
			$taxonomies         = get_object_taxonomies( 'awsm_job_openings', 'objects' );
			$show_icon          = get_option( 'awsm_jobs_show_specs_icon', 'show_icon' );
			$is_specs_clickable = get_option( 'awsm_jobs_make_specs_clickable' );
			foreach ( $taxonomies as $taxonomy => $options ) {
				if ( ! in_array( $taxonomy, $spec_keys, true ) ) {
					continue;
				}
				$display = true;
				if ( ! empty( $listing_specs ) ) {
					$display = false;
					if ( isset( $listing_specs['specs'] ) && is_array( $listing_specs['specs'] ) && in_array( $taxonomy, $listing_specs['specs'] ) ) {
						$display = true;
					}
				}
				if ( $display ) {
					$terms = get_the_terms( $post_id, $taxonomy );
					if ( ! empty( $terms ) ) {
						$spec_label = $spec_icon = $spec_terms = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
						if ( $display_label ) {
							$spec_label = '<span class="awsm-job-specification-label"><strong>' . esc_html( $options->label ) . ': </strong></span>';
						}
						foreach ( $filter_data as $filter ) {
							if ( $taxonomy === $filter['taxonomy'] ) {
								if ( ! empty( $filter['icon'] ) ) {
									if ( ! is_singular( 'awsm_job_openings' ) || $show_icon === 'show_icon' ) {
										$spec_icon = sprintf( '<i class="awsm-job-icon-%1$s"></i>', esc_attr( $filter['icon'] ) );
									}
								}
							}
						}
						foreach ( $terms as $term ) {
							$term_link = get_term_link( $term );
							if ( ! is_singular( 'awsm_job_openings' ) || $is_specs_clickable !== 'make_clickable' || is_wp_error( $term_link ) ) {
								$spec_terms .= '<span class="awsm-job-specification-term">' . esc_html( $term->name ) . '</span> ';
							} else {
								$spec_terms .= sprintf( '<a href="%2$s" class="awsm-job-specification-term">%1$s</a> ', esc_html( $term->name ), esc_url( $term_link ) );
							}
						}
						$spec_content .= sprintf( '<div class="awsm-job-specification-item">%1$s</div>', $spec_icon . $spec_label . $spec_terms );
					}
				}
			}
		}
		if ( ! empty( $spec_content ) ) {
			$spec_content = sprintf( '<div class="awsm-job-specification-wrapper">%1$s</div>', $spec_content );
		}
		return apply_filters( 'awsm_specification_content', $spec_content, $post_id );
	}

	public static function display_specifications_content( $post_id, $pos, $echo = true ) {
		$content       = '';
		$show_job_spec = get_option( 'awsm_jobs_specification_job_detail', 'show_in_detail' );
		$spec_position = get_option( 'awsm_jobs_specs_position', 'below_content' );
		if ( $spec_position === $pos && $show_job_spec === 'show_in_detail' ) {
			$content = sprintf( '<div class="awsm-job-specifications-container %2$s"><div class="awsm-job-specifications-row">%1$s</div></div>', self::get_specifications_content( $post_id, true ), esc_attr( 'awsm_job_spec_' . $pos ) );
		}
		if ( $echo ) {
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $content;
		}
	}

	public function job_spec_structured_data( $post ) {
		$data              = array();
		$default_emp_types = array(
			'FULL_TIME'  => __( 'Full Time', 'wp-job-openings' ),
			'PART_TIME'  => __( 'Part Time', 'wp-job-openings' ),
			'CONTRACTOR' => __( 'Freelance', 'wp-job-openings' ),
			'TEMPORARY'  => __( 'Temporary', 'wp-job-openings' ),
			'INTERN'     => __( 'Intern', 'wp-job-openings' ),
			'VOLUNTEER'  => __( 'Volunteer', 'wp-job-openings' ),
			'PER_DIEM'   => __( 'Per Diem', 'wp-job-openings' ),
			'OTHER'      => __( 'Other', 'wp-job-openings' ),
		);
		$default_emp_types = array_flip( array_map( 'sanitize_title', $default_emp_types ) );
		if ( ! empty( $default_emp_types ) ) {
			if ( taxonomy_exists( 'job-type' ) ) {
				$emp_types = get_the_terms( $post->ID, 'job-type' );
				if ( ! empty( $emp_types ) ) {
					$data['employmentType'] = array();
					foreach ( $emp_types as $emp_type ) {
						$slug = $emp_type->slug;
						if ( array_key_exists( $slug, $default_emp_types ) ) {
							$data['employmentType'][] = $default_emp_types[ $slug ];
						}
					}
					if ( count( $data['employmentType'] ) === 1 ) {
						$data['employmentType'] = $data['employmentType'][0];
					}
				}
			}
		}

		if ( taxonomy_exists( 'job-location' ) ) {
			$locations = get_the_terms( $post->ID, 'job-location' );
			if ( ! empty( $locations ) ) {
				$data['jobLocation'] = array();
				foreach ( $locations as $location ) {
					$data['jobLocation'][] = array(
						'@type'   => 'Place',
						'address' => $location->name,
					);
				}
				if ( count( $data['jobLocation'] ) === 1 ) {
					$data['jobLocation'] = $data['jobLocation'][0];
				}
			}
		}
		return apply_filters( 'awsm_job_spec_structured_data', $data );
	}

	public function get_structured_data() {
		global $post;
		if ( $post->post_status === 'expired' ) {
			return;
		}

		$post_id         = $post->ID;
		$data            = array(
			'@context'    => 'http://schema.org/',
			'@type'       => 'JobPosting',
			'title'       => wp_strip_all_tags( get_the_title() ),
			'description' => get_the_content(),
			'datePosted'  => get_post_time( 'c' ),
		);
		$expiry_on_list  = get_post_meta( $post_id, 'awsm_set_exp_list', true );
		$expiration_date = get_post_meta( $post_id, 'awsm_job_expiry', true );
		if ( $expiry_on_list === 'set_listing' && ! empty( $expiration_date ) ) {
			$data['validThrough'] = date( 'c', strtotime( $expiration_date ) );
		}
		$company_name = get_option( 'awsm_job_company_name' );
		if ( ! empty( $company_name ) ) {
			$data['hiringOrganization'] = array(
				'@type'  => 'Organization',
				'name'   => $company_name,
				'sameAs' => esc_url( home_url() ),
			);
		}
		$job_spec_data = $this->job_spec_structured_data( $post );
		if ( ! empty( $job_spec_data ) ) {
			$data = array_merge( $data, $job_spec_data );
		}
		return apply_filters( 'awsm_job_structured_data', $data );
	}

	public function display_structured_data() {
		if ( ! is_singular( 'awsm_job_openings' ) ) {
			return;
		}

		$data = $this->get_structured_data();
		if ( ! empty( $data ) ) {
			printf( '<script type="application/ld+json">%s</script>', wp_json_encode( $data ) );
		}
	}
}

$awsm_job_openings = AWSM_Job_Openings::init();

// activation
register_activation_hook( __FILE__, array( $awsm_job_openings, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $awsm_job_openings, 'deactivate' ) );
