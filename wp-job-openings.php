<?php
/**
 * Plugin Name: WP Job Openings
 * Plugin URI: https://wpjobopenings.com/
 * Description: Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.
 * Author: AWSM Innovations
 * Author URI: https://awsm.in/
 * Version: 3.4.6
 * Requires at least: 4.8
 * Requires PHP: 5.6
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text domain: wp-job-openings
 * Domain Path: /languages
 */
/**
 * WP Job Openings Plugin
 *
 * Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.
 *
 * @package wp-job-openings
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
	define( 'AWSM_JOBS_PLUGIN_VERSION', '3.4.6' );
}
if ( ! defined( 'AWSM_JOBS_UPLOAD_DIR_NAME' ) ) {
	define( 'AWSM_JOBS_UPLOAD_DIR_NAME', 'awsm-job-openings' );
}
if ( ! defined( 'AWSM_JOBS_DEBUG' ) ) {
	define( 'AWSM_JOBS_DEBUG', false );
}

// Helper functions
require_once AWSM_JOBS_PLUGIN_DIR . '/inc/helper-functions.php';

class AWSM_Job_Openings {
	private static $instance = null;

	private static $rating_notice_active = false;

	protected $unique_listing_id = 1;

	public $awsm_core = null;

	public $awsm_form = null;

	public function __construct() {
		// Require Classes.
		self::load_classes();

		// Initialize Classes.
		$this->awsm_core = AWSM_Job_Openings_Core::init();
		AWSM_Job_Openings_UI_Builder::init();
		$this->awsm_form = AWSM_Job_Openings_Form::init();
		AWSM_Job_Openings_Mail_Customizer::init();
		AWSM_Job_Openings_Filters::init();
		if ( is_admin() ) {
			AWSM_Job_Openings_Overview::init();
			AWSM_Job_Openings_Meta::init();
			AWSM_Job_Openings_Settings::init( $this->awsm_core );
			AWSM_Job_Openings_Info::init();
		}

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'upgrade' ) );
		add_action( 'after_setup_theme', array( $this, 'template_functions' ) );
		add_action( 'init', array( $this, 'init_actions' ) );
		add_action( 'wp_head', array( $this, 'awsm_wp_head' ) );
		add_action( 'awsm_check_for_expired_jobs', array( $this, 'check_date_and_change_status' ) );
		add_action( 'awsm_jobs_email_digest', array( $this, 'send_email_digest' ) );
		add_action( 'awsm_job_application_submitted', array( $this, 'plugin_rating_check' ) );
		add_action( 'wp_loaded', array( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'awsm_enqueue_scripts' ) );
		add_action( 'template_redirect', array( $this, 'redirect_attachment_page' ), 1 );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'wp_ajax_awsm_view_count', array( $this, 'job_views_handler' ) );
		add_action( 'wp_ajax_nopriv_awsm_view_count', array( $this, 'job_views_handler' ) );
		add_action( 'wp_footer', array( $this, 'display_structured_data' ) );
		$this->admin_actions();

		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'the_content', array( $this, 'awsm_jobs_content' ), 100 );
		add_filter( 'single_template', array( $this, 'jobs_single_template' ) );
		add_filter( 'archive_template', array( $this, 'jobs_archive_template' ) );
		add_filter( 'wp_robots', array( $this, 'no_robots' ) );
		$this->admin_filters();
		add_shortcode( 'awsmjobs', array( $this, 'awsm_jobs_shortcode' ) );
		add_action( 'transition_post_status', array( $this, 'expiry_notification_handler' ), 10, 3 );
		add_filter( 'display_post_states', array( $this, 'display_job_post_states' ), 10, 2 );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public static function load_classes() {
		$prefix  = 'class-awsm-job-openings';
		$classes = array( 'core', 'ui-builder', 'filters', 'mail-customizer', 'form', 'third-party' );
		foreach ( $classes as $class ) {
			require_once AWSM_JOBS_PLUGIN_DIR . "/inc/{$prefix}-{$class}.php";
		}
		if ( is_admin() ) {
			$classes = array( 'overview', 'meta', 'settings', 'info' );
			foreach ( $classes as $class ) {
				require_once AWSM_JOBS_PLUGIN_DIR . "/admin/{$prefix}-{$class}.php";
			}
		}
	}

	public function activate() {
		$this->register_default_settings();
		$this->awsm_core->register();
		$this->insert_default_specs_terms();
		$this->create_page_when_activate();
		flush_rewrite_rules();
		$this->setup_page_init();
	}

	public function deactivate() {
		$this->clear_transients();
		$this->clear_cron_jobs();
		$this->awsm_core->unregister();
		flush_rewrite_rules();
	}

	public static function log( $data, $prefix = '' ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && defined( 'AWSM_JOBS_DEBUG' ) && AWSM_JOBS_DEBUG ) {
			if ( is_string( $data ) ) {
				error_log( 'WP Job Openings:' . $prefix . ': ' . $data );
			} else {
				error_log( 'WP Job Openings:' . $prefix . ': ' . json_encode( $data, JSON_PRETTY_PRINT ) );
			}
		}
	}

	private function register_default_settings() {
		if ( ! class_exists( 'AWSM_Job_Openings_Settings' ) ) {
			require_once AWSM_JOBS_PLUGIN_DIR . '/admin/class-awsm-job-openings-settings.php';
		}
		AWSM_Job_Openings_Settings::register_defaults();
	}

	private function insert_default_specs_terms() {
		if ( get_option( 'awsm_jobs_insert_default_specs_terms' ) == 1 ) {
			return;
		}
		$specs = get_option( 'awsm_jobs_filter' );
		$this->awsm_jobs_taxonomies( $specs );
		$this->insert_specs_terms( $specs );
		update_option( 'awsm_jobs_insert_default_specs_terms', 1 );
	}

	public function setup_page_init() {
		$plugin_version = get_option( 'awsm_jobs_plugin_version' );
		$company_name   = get_option( 'awsm_job_company_name' );
		if ( empty( $plugin_version ) && empty( $company_name ) ) {
			set_transient( '_awsm_activation_redirect', true, MINUTE_IN_SECONDS );
		}
	}

	private function clear_transients() {
		delete_transient( '_awsm_activation_redirect' );
		delete_transient( '_awsm_add_ons_data' );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'wp-job-openings', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function upgrade() {
		if ( intval( get_option( 'awsm_jobs_upgrade_count' ) ) !== 1 ) {
			$upload_dir = wp_upload_dir();
			$base_dir   = trailingslashit( $upload_dir['basedir'] );
			$upload_dir = $base_dir . AWSM_JOBS_UPLOAD_DIR_NAME;
			$this->index_to_upload_dir( $upload_dir );
			update_option( 'awsm_jobs_upgrade_count', 1 );
		}
	}

	public function index_to_upload_dir( $dir ) {
		$index_file = $dir . '/index.php';
		if ( ! file_exists( $index_file ) ) {
			file_put_contents( $index_file, "<?php\n\n//Silence is golden.\n" );
		}
		$sub_dirs = array_filter( glob( $dir . '/*' ), 'is_dir' );
		foreach ( $sub_dirs as $sub_dir ) {
			$this->index_to_upload_dir( $sub_dir );
		}
	}

	public function template_functions() {
		include_once AWSM_JOBS_PLUGIN_DIR . '/inc/template-functions.php';
	}

	public function init_actions() {
		$this->awsm_openings_cron_job();
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
			add_action( 'wp_ajax_awsm_plugin_rating', array( $this, 'plugin_rating' ) );
			add_action( 'admin_notices', array( $this, 'plugin_rating_notice_handler' ) );
			// Add custom status to status dropdown under post submit meta box (existing and new) for job openings.
			add_action( 'admin_footer-post.php', array( $this, 'job_submit_meta_box_custom_status' ) );
			add_action( 'admin_footer-post-new.php', array( $this, 'job_submit_meta_box_custom_status' ) );
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
			if ( ! empty( $post_id ) ) {
				update_option( 'awsm_jobs_default_listing_page_id', $post_id );
			}
		}
	}

	public static function add_shortcode_to_page( $page_id ) {
		$post_content = get_post_field( 'post_content', $page_id );
		if ( ! has_shortcode( $post_content, 'awsmjobs' ) ) {
			$post_content .= '<p>[awsmjobs]</p>';
		}
		$page_data = array(
			'ID'           => $page_id,
			'post_content' => $post_content,
		);
		wp_update_post( $page_data );
	}

	public function awsm_jobs_shortcode( $atts ) {
		if ( ! function_exists( 'awsm_jobs_query' ) ) {
			return;
		}

		/**
		 * Filters the shortcode attributes and their defaults.
		 *
		 * @since 1.6.0
		 *
		 * @param array $pairs List of supported attributes and their defaults.
		 */
		$pairs          = apply_filters(
			'awsm_jobs_shortcode_defaults',
			array(
				'uid'        => $this->unique_listing_id,
				'filters'    => get_option( 'awsm_enable_job_filter_listing' ) !== 'enabled' ? 'no' : 'yes',
				'listings'   => get_option( 'awsm_jobs_list_per_page' ),
				'loadmore'   => 'yes',
				'pagination' => get_option( 'awsm_jobs_pagination_type', 'modern' ),
				'specs'      => '',
			)
		);
		$shortcode_atts = shortcode_atts( $pairs, $atts, 'awsmjobs' );

		$this->unique_listing_id++;

		ob_start();
		include self::get_template_path( 'job-openings-view.php' );
		$content = ob_get_clean();

		/**
		 * Filters the shortcode output content.
		 *
		 * @since 1.6.0
		 *
		 * @param string $content Shortcode content.
		 * @param array $shortcode_atts Combined and filtered shortcode attribute list.
		 */
		return apply_filters( 'awsm_jobs_shortcode_output_content', $content, $shortcode_atts );
	}

	public function register_widgets() {
		$widgets = array( 'recent-jobs', 'dashboard' );
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
			'author'                => esc_attr__( 'Author', 'default' ),
			'awsm_job_applications' => esc_attr__( 'Applications', 'wp-job-openings' ),
			'awsm_job_expiry'       => esc_attr__( 'Expiry', 'wp-job-openings' ),
			'awsm_job_post_views'   => esc_attr__( 'Views', 'wp-job-openings' ),
			'awsm_job_conversion'   => esc_attr__( 'Conversion', 'wp-job-openings' ),
		);
		return $columns;
	}

	public static function get_application_edit_link( $id ) {
		$link             = '';
		$post_type_object = get_post_type_object( 'awsm_job_application' );
		if ( ! empty( $post_type_object ) && $post_type_object->_edit_link ) {
			$link = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $id ) );
		}
		return $link;
	}

	public static function get_all_applications( $fields = 'ids', $extra_args = array() ) {
		$defaults = array(
			'post_type'   => 'awsm_job_application',
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'post_status' => 'any',
			'fields'      => $fields,
		);
		$args     = wp_parse_args( $extra_args, $defaults );
		/**
		 * Filters the arguments to retrieve all applications.
		 *
		 * @since 3.3.3
		 *
		 * @param array $args Arguments to retrieve applications.
		 * @param array $extra_args Extra arguments.
		 * @param array $defaults Default arguments to retrieve applications.
		 */
		$args         = apply_filters( 'awsm_all_applications_args', $args, $extra_args, $defaults );
		$applications = get_posts( $args );
		return $applications;
	}

	public static function get_recent_applications( $number_posts = 5, $most_recent = true, $fields = 'all' ) {
		$args = array(
			'numberposts' => $number_posts,
			'post_status' => 'any',
		);
		if ( $most_recent ) {
			$args['post_status'] = 'publish';
			$args['date_query']  = array(
				array(
					'after'     => '1 day ago',
					'inclusive' => true,
				),
			);
		}
		$applications = self::get_all_applications( $fields, $args );
		return $applications;
	}

	public static function get_applications( $job_id, $fields = 'all' ) {
		$applications = get_children(
			array(
				'post_parent' => $job_id,
				'post_type'   => 'awsm_job_application',
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'fields'      => $fields,
			)
		);
		return $applications;
	}

	public function awsm_job_custom_column_member_data( $column, $post_id ) {
		$application_count = count( self::get_applications( $post_id, 'ids' ) );
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
					echo ( $expiry_on_list === 'set_listing' && ! empty( $job_expiry ) ) ? esc_html( date_i18n( get_awsm_jobs_date_format( 'expiry-admin' ), strtotime( $job_expiry ) ) ) : $default_display; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
			'cb' => '<input type="checkbox" />',
		);
		if ( current_user_can( 'edit_others_applications' ) ) {
			$columns['awsm-photo'] = '';
		}
		$columns['title']           = esc_attr__( 'Applicant', 'wp-job-openings' );
		$columns['application_id']  = esc_attr__( 'ID', 'wp-job-openings' );
		$columns['applied_for']     = esc_attr__( 'Job', 'wp-job-openings' );
		$columns['submission_time'] = esc_attr__( 'Applied on', 'wp-job-openings' );
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
				if ( current_user_can( 'edit_others_applications' ) ) {
					edit_post_link( esc_html( $post_id ) );
				} else {
					echo esc_html( $post_id );
				}
				break;

			case 'applied_for':
				if ( current_user_can( 'edit_post', $post_id ) ) {
					$job_link = get_edit_post_link( $job_id );
					if ( empty( $job_link ) ) {
						echo esc_html( $job_name );
					} else {
						printf( '<a href="%2$s" title="%3$s">%1$s</a>', esc_html( $job_name ), esc_url( get_edit_post_link( $job_id ) ), esc_attr( __( 'View Job: ', 'wp-job-openings' ) . $job_name ) );
					}
				} else {
					echo esc_html( $job_name );
				}
				break;

			case 'submission_time':
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

	public function job_submit_meta_box_custom_status() {
		global $post_type;
		if ( $post_type !== 'awsm_job_openings' ) {
			return;
		}

		$status = array(
			'publish' => __( 'Published', 'default' ),
			'expired' => __( 'Expired', 'wp-job-openings' ),
			'future'  => __( 'Scheduled', 'default' ),
			'pending' => __( 'Pending Review', 'default' ),
			'draft'   => __( 'Draft', 'default' ),
		);
		/**
		 * Filters the status array for submit meta box for job openings.
		 *
		 * @since 2.1.0
		 *
		 * @param array $status Job status array.
		 */
		$status = apply_filters( 'awsm_job_post_status', $status );

		global $post;
		if ( $post->post_status === 'future' ) {
			unset( $status['publish'], $status['expired'] );
		} else {
			unset( $status['future'] );
		}
		$options        = '';
		$display_status = '';
		foreach ( $status as $name => $label ) {
			$selected = selected( $post->post_status, $name, false );
			if ( ! empty( $selected ) ) {
				$display_status = $label;
			}
			$options .= sprintf( '<option value="%2$s"%3$s>%1$s</option>', esc_html( $label ), esc_attr( $name ), $selected );
		}
		?>
			<script>
				jQuery(document).ready(function($) {
					<?php if ( ! empty( $display_status ) ) : ?>
						$('#post-status-display').text('<?php echo esc_html( $display_status ); ?>');
					<?php endif; ?>

					$('#post_status').html('<?php echo wp_slash( $options ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>');
				});
			</script>
		<?php
	}

	public function awsm_openings_cron_job() {
		if ( ! wp_next_scheduled( 'awsm_check_for_expired_jobs' ) ) {
			wp_schedule_event( time(), 'hourly', 'awsm_check_for_expired_jobs' );
		}
		// Email digest.
		$email_digest = get_option( 'awsm_jobs_email_digest', 'enable' );
		if ( $email_digest === 'enable' && ! wp_next_scheduled( 'awsm_jobs_email_digest' ) ) {
			wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'awsm_jobs_email_digest' );
		}
	}

	public function clear_cron_jobs() {
		wp_clear_scheduled_hook( 'awsm_check_for_expired_jobs' );
		wp_clear_scheduled_hook( 'awsm_jobs_email_digest' );
	}

	public function check_date_and_change_status() {
		$current_date  = gmdate( 'Y-m-d' );
		$selected_zone = get_option( 'awsm_jobs_timezone' );
		if ( is_array( $selected_zone ) && isset( $selected_zone['gmt_offset'] ) && isset( $selected_zone['timezone_string'] ) ) {
			$timezone = self::get_timezone_string( $selected_zone );
			if ( $timezone !== 'UTC' ) {
				$date_timezone = new DateTimeZone( $timezone );
				$datetime      = new DateTime( 'now', $date_timezone );
				$current_date  = $datetime->format( 'Y-m-d' );
			}
		}

		$args = array(
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
					'value'   => $current_date,
					'type'    => 'DATE',
					'compare' => '<',
				),
			),
		);
		/**
		 * Filters the arguments for the jobs query for automatic expiration.
		 *
		 * @since 2.3.0
		 *
		 * @param array $args arguments.
		 */
		$args = apply_filters( 'awsm_check_for_expired_jobs_query_args', $args );

		$query = new WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();
			// still doing some usual checking even if meta query is used!
			$expiry_on_list = get_post_meta( get_the_ID(), 'awsm_set_exp_list', true );
			if ( $expiry_on_list === 'set_listing' ) {
				$jobs                = array();
				$jobs['ID']          = get_the_ID();
				$jobs['post_status'] = 'expired';
				wp_update_post( $jobs );
			}
		}
	}

	public static function get_timezone_string( $selected_zone ) {
		$timezone_string = 'UTC';
		if ( ! empty( $selected_zone['timezone_string'] ) ) {
			$timezone_string = $selected_zone['timezone_string'];
		} elseif ( ! empty( $selected_zone['gmt_offset'] ) && $selected_zone['gmt_offset'] !== 0 ) {
			$offset          = (float) $selected_zone['gmt_offset'];
			$hours           = (int) $offset;
			$minutes         = ( $offset - $hours );
			$sign            = ( $offset < 0 ) ? '-' : '+';
			$abs_hour        = abs( $hours );
			$abs_mins        = abs( $minutes * 60 );
			$timezone_string = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
		}

		return $timezone_string;
	}

	public function send_email_digest() {
		$to = get_option( 'awsm_hr_email_address' );
		if ( ! class_exists( 'AWSM_Job_Openings_Settings' ) ) {
			require_once AWSM_JOBS_PLUGIN_DIR . '/admin/class-awsm-job-openings-settings.php';
		}
		$default_from_email = AWSM_Job_Openings_Settings::awsm_from_email();
		if ( ! empty( $to ) ) {
			$applications = self::get_recent_applications( 3 );
			if ( ! empty( $applications ) ) {
				$company_name = get_option( 'awsm_job_company_name', '' );
				$from         = ( ! empty( $company_name ) ) ? $company_name : get_option( 'blogname' );
				$admin_email  = get_option( 'admin_email' );
				$from_email   = get_option( 'awsm_jobs_admin_from_email_notification', $default_from_email );

				ob_start();
				include self::get_template_path( 'email-digest.php', 'mail' );
				$mail_content = ob_get_clean();

				/**
				 * Filters the daily email digest template content.
				 *
				 * @since 2.0.0
				 *
				 * @param string $mail_content Mail template content.
				 */
				$mail_content = apply_filters( 'awsm_jobs_email_digest_template_content', $mail_content );

				if ( ! empty( $mail_content ) ) {
					$tags         = self::get_mail_generic_template_tags(
						array(
							'admin_email'        => $admin_email,
							'hr_email'           => $to,
							'company_name'       => $company_name,
							'default_from_email' => $default_from_email,
						)
					);
					$tag_names    = array_keys( $tags );
					$tag_values   = array_values( $tags );
					$from_email   = str_replace( $tag_names, $tag_values, $from_email );
					$mail_content = str_replace( $tag_names, $tag_values, $mail_content );
					/**
					 * Filters the daily email digest headers.
					 *
					 * @since 2.0.0
					 *
					 * @param array $headers Additional headers
					 */
					$headers = apply_filters(
						'awsm_jobs_email_digest_mail_headers',
						array(
							'content_type' => 'Content-Type: text/html; charset=UTF-8',
							'from'         => sprintf( 'From: %1$s <%2$s>', $from, $from_email ),
						)
					);

					/**
					 * Filters the daily email digest subject.
					 *
					 * @since 2.0.0
					 *
					 * @param string $subject Email subject.
					 */
					$subject = apply_filters( 'awsm_jobs_email_digest_subject', esc_html__( 'Email Digest - WP Job Openings', 'wp-job-openings' ) );

					add_filter( 'wp_mail_content_type', 'awsm_jobs_mail_content_type' );
					wp_mail( $to, $subject, $mail_content, array_values( $headers ) );
					remove_filter( 'wp_mail_content_type', 'awsm_jobs_mail_content_type' );
				}
			}
		}
	}

	public static function get_mail_generic_template_tags( $options = array() ) {
		$company_name       = isset( $options['company_name'] ) ? $options['company_name'] : get_option( 'awsm_job_company_name' );
		$admin_email        = isset( $options['admin_email'] ) ? $options['admin_email'] : get_option( 'admin_email' );
		$hr_email           = isset( $options['hr_email'] ) ? $options['hr_email'] : get_option( 'awsm_hr_email_address', '' );
		$default_from_email = isset( $options['default_from_email'] ) ? $options['default_from_email'] : get_option( 'awsm_jobs_from_email_notification', '' );

		$tags = array(
			'{site-title}'         => esc_html( get_bloginfo( 'name' ) ),
			'{site-tagline}'       => esc_html( get_bloginfo( 'description' ) ),
			'{site-url}'           => esc_url( site_url( '/' ) ),
			'{company}'            => esc_html( $company_name ),
			'{admin-email}'        => esc_html( $admin_email ),
			'{hr-email}'           => esc_html( $hr_email ),
			'{default-from-email}' => $default_from_email,
		);

		/**
		 * Filters the mail generic template tags.
		 *
		 * @since 2.0.0
		 *
		 * @param array $tags Mail template tags.
		 * @param array $options Settings values.
		 */
		return apply_filters( 'awsm_jobs_mail_generic_template_tags', $tags, $options );
	}

	public static function get_overview_data() {
		$jobs_count         = (array) wp_count_posts( 'awsm_job_openings' );
		$applications_count = (array) wp_count_posts( 'awsm_job_application' );
		unset( $jobs_count['auto-draft'], $applications_count['auto-draft'] );
		$total_jobs         = array_sum( $jobs_count );
		$total_applications = array_sum( $applications_count );
		$data               = array(
			'active_jobs'        => $jobs_count['publish'],
			'total_jobs'         => $total_jobs,
			'new_applications'   => $applications_count['publish'],
			'total_applications' => $total_applications,
		);
		/**
		 * Filters the overview data.
		 *
		 * @since 3.3.3
		 *
		 * @param array $data Overview data.
		 */
		return apply_filters( 'awsm_jobs_overview_data', $data );
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
				'posts_per_page'   => -1,
				'post_type'        => 'awsm_job_openings',
				'post_status'      => array( 'publish', 'expired' ),
				'suppress_filters' => false,
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
				printf( '<option value="%1$d"%3$s>%2$s</option>', intval( $post_id ), esc_html( $post_title ), esc_attr( $selected ) );
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
			if ( ! function_exists( 'wp_robots_no_robots' ) && $post->post_status === 'expired' && get_option( 'awsm_jobs_expired_jobs_block_search' ) === 'block_expired' ) {
				wp_no_robots();
			}
		}
	}

	public function no_robots( $robots ) {
		if ( is_singular( 'awsm_job_openings' ) ) {
			global $post;
			if ( isset( $post ) && $post->post_status === 'expired' && get_option( 'awsm_jobs_expired_jobs_block_search' ) === 'block_expired' ) {
				$robots['noindex']  = true;
				$robots['nofollow'] = true;
			}
		}
		return $robots;
	}

	public function job_views_handler() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
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
		// phpcs:enable
	}

	public function no_script_msg() {
		?>
		<noscript>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'JavaScript is required! Please enable it in your browser.', 'wp-job-openings' ); ?></p>
			</div>
		</noscript>
		<?php
	}

	public static function plugin_rating_notice( $rating_url, $rating_env, $context = 'job' ) {
		if ( ! self::$rating_notice_active ) :
			$posts_count = get_option( "awsm_plugin_rating_{$context}_count" );
			$rate_later  = get_transient( "_awsm_{$context}_ctx_plugin_rate_later" );

			if ( is_array( $posts_count ) && $posts_count['active'] && $rate_later !== 'later' ) :
				if ( ! wp_script_is( 'awsm-job-admin' ) ) {
					wp_enqueue_script( 'awsm-job-admin' );
				}

				self::$rating_notice_active = true;
				/* translators: %1$s: opening html tag, %2$s: closing html tag, %3$s: Jobs count, %4$s: Plugin rating site */
				$notice = esc_html__( 'That\'s awesome! You have just published %3$sth job posting on your wesbite using %1$sWP Job Openings%2$s. Could you please do us a BIG favor and give it a %1$s5-star%2$s rating on %4$s? Just to help us spread the word and boost our motivation.', 'wp-job-openings' );
				if ( $context === 'application' ) {
					/* translators: %1$s: opening html tag, %2$s: closing html tag, %3$s: Applications count, %4$s: Plugin rating site */
					$notice = esc_html__( 'You have received over %1$s%3$s%2$s job applications through %1$sWP Job Openings%2$s. That\'s awesome! May we ask you to give it a %1$s5-Star%2$s rating on %4$s. It will help us spread the word and boost our motivation.', 'wp-job-openings' );
				}
				?>
				<div class='awsm-job-plugin-rating-wrapper notice notice-info notice'>
					<?php printf( '<p>' . $notice . '</p>', '<strong>', '</strong>', esc_html( $posts_count['current'] ), esc_html( $rating_env ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<p>
						<a class="button button-primary" href='<?php echo esc_url( $rating_url ); ?>' target="_blank"><?php echo esc_html__( 'Ok, you deserve it', 'wp-job-openings' ); ?></a>
						<a class="awsm-job-plugin-rating-action button button-secondary" href='#' data-status="done" data-context="<?php echo esc_attr( $context ); ?>"><?php echo esc_html__( 'I already did', 'wp-job-openings' ); ?></a>
						<a class="awsm-job-plugin-rating-action button button-secondary" href='#' data-status="later" data-context="<?php echo esc_attr( $context ); ?>"><?php echo esc_html__( 'Maybe later', 'wp-job-openings' ); ?></a>
					</p>
				</div>
				<?php
			endif;
		endif;
	}

	public function plugin_rating_notice_handler() {
		$rating_env = apply_filters( 'awsm_jobs_plugin_rating_env', 'WordPress' );
		$rating_url = apply_filters( 'awsm_jobs_plugin_rating_url', 'https://wordpress.org/support/plugin/wp-job-openings/reviews/?filter=5' );

		$rated = intval( get_option( 'awsm_jobs_plugin_rating' ) );
		if ( $rated !== 1 ) {
			// Job Context.
			self::plugin_rating_notice( $rating_url, $rating_env );

			// Application context.
			self::plugin_rating_notice( $rating_url, $rating_env, 'application' );
		}
	}

	public function enable_plugin_rating( $posts_count, $context = 'job' ) {
		if ( $posts_count >= 10 ) {
			$ctx_count = get_option( "awsm_plugin_rating_{$context}_count" );
			if ( empty( $ctx_count ) || ! is_array( $ctx_count ) ) {
				$count_details = array(
					'active'   => true,
					'current'  => $posts_count,
					'previous' => $posts_count,
				);
				update_option( "awsm_plugin_rating_{$context}_count", $count_details );
			} else {
				$ctx_count['current'] = $posts_count;
				$rate_later           = get_transient( "_awsm_{$context}_ctx_plugin_rate_later" );
				if ( $rate_later !== 'later' ) {
					$prev_count = intval( $ctx_count['previous'] );
					if ( ( $posts_count - $prev_count ) >= 25 || $posts_count < $prev_count ) {
						$ctx_count['active']   = true;
						$ctx_count['previous'] = $posts_count;
					}
				} else {
					$ctx_count['active'] = false;
				}
				update_option( "awsm_plugin_rating_{$context}_count", $ctx_count );
			}
		}
	}

	public function plugin_rating_check() {
		$rated_status = intval( get_option( 'awsm_jobs_plugin_rating' ) );
		if ( $rated_status !== 1 ) {
			$count_details      = wp_count_posts( 'awsm_job_application' );
			$applications_count = $count_details->publish;
			$other_status       = array( 'progress', 'shortlist', 'reject', 'select' );
			foreach ( $other_status as $status ) {
				$applications_count += isset( $count_details->$status ) ? $count_details->$status : 0;
			}
			$this->enable_plugin_rating( $applications_count, 'application' );
		}
	}

	public function plugin_rating() {
		$response = array(
			'code'   => 'error',
			'errors' => array(),
		);

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'awsm-admin-nonce' ) ) {
			$response['errors'][] = esc_html__( 'Invalid request!', 'wp-job-openings' );
		}

		$contexts = array( 'job', 'application' );
		$context  = isset( $_POST['context'] ) ? sanitize_text_field( $_POST['context'] ) : '';
		if ( empty( $context ) || ! in_array( $context, $contexts ) ) {
			$response['errors'][] = esc_html__( 'Invalid context!', 'wp-job-openings' );
		}

		if ( count( $response['errors'] ) === 0 ) {
			if ( isset( $_POST['status'] ) && $_POST['status'] === 'done' ) {
				update_option( 'awsm_jobs_plugin_rating', 1 );
			} else {
				set_transient( "_awsm_{$context}_ctx_plugin_rate_later", 'later', WEEK_IN_SECONDS );
				$ctx_count = get_option( "awsm_plugin_rating_{$context}_count" );
				if ( is_array( $ctx_count ) ) {
					$ctx_count['active'] = false;
					update_option( "awsm_plugin_rating_{$context}_count", $ctx_count );
				}
			}
			$response['code'] = 'success';
		}
		wp_send_json( $response );
	}

	public function register_scripts() {
		wp_register_style( 'awsm-jobs-general', AWSM_JOBS_PLUGIN_URL . '/assets/css/general.min.css', array(), AWSM_JOBS_PLUGIN_VERSION, 'all' );
	}

	public function awsm_enqueue_scripts() {
		wp_enqueue_style( 'awsm-jobs-general' );
		wp_enqueue_style( 'awsm-jobs-style', AWSM_JOBS_PLUGIN_URL . '/assets/css/style.min.css', array( 'awsm-jobs-general' ), AWSM_JOBS_PLUGIN_VERSION, 'all' );

		$is_recaptcha_set = $this->awsm_form->is_recaptcha_set();
		if ( is_singular( 'awsm_job_openings' ) && $is_recaptcha_set ) {
			wp_enqueue_script( 'g-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '2.0', false );
		}
		wp_enqueue_script( 'awsm-job-scripts', AWSM_JOBS_PLUGIN_URL . '/assets/js/script.min.js', array( 'jquery' ), AWSM_JOBS_PLUGIN_VERSION, true );

		$enable_search = get_option( 'awsm_enable_job_search' ) === 'enable' && isset( $_GET['jq'] );
		global $post;

		$localized_script_data = array(
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'is_tax_archive'     => is_tax(),
			'is_search'          => $enable_search ? sanitize_text_field( $_GET['jq'] ) : '',
			'job_id'             => is_singular( 'awsm_job_openings' ) ? $post->ID : 0,
			'wp_max_upload_size' => ( wp_max_upload_size() ) ? ( wp_max_upload_size() ) : 0,
			'deep_linking'       => array(
				'search'     => true,
				'spec'       => true,
				'pagination' => true,
			),
			'i18n'               => array(
				'loading_text'   => esc_html__( 'Loading...', 'wp-job-openings' ),
				'form_error_msg' => array(
					'general'         => esc_html__( 'Error in submitting your application. Please try again later!', 'wp-job-openings' ),
					'file_validation' => esc_html__( 'The file you have selected is too large.', 'wp-job-openings' ),
				),
			),
			'vendors'            => array(
				'selectric'         => true,
				'jquery_validation' => true,
			),
		);
		/**
		 * Filters the public script localized data.
		 *
		 * @since 2.3.0
		 *
		 * @param array $localized_script_data Localized data array.
		 */
		$localized_script_data = apply_filters( 'awsm_jobs_localized_script_data', $localized_script_data );
		wp_localize_script( 'awsm-job-scripts', 'awsmJobsPublic', $localized_script_data );
	}

	public function awsm_admin_enqueue_scripts() {
		$is_job_page = false;
		$screen      = get_current_screen();
		$script_deps = array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'wp-color-picker', 'wp-util' );
		if ( ! empty( $screen ) ) {
			$post_type = $screen->post_type;
			if ( ( $post_type === 'awsm_job_openings' ) || ( $post_type === 'awsm_job_application' ) ) {
				$is_job_page = true;

				if ( $screen->id === 'awsm_job_openings_page_awsm-jobs-settings' ) {
					wp_enqueue_media();
					$script_deps[] = 'media-models';
				}
			}
		}

		wp_register_style( 'awsm-job-admin-global', AWSM_JOBS_PLUGIN_URL . '/assets/css/admin-global.min.css', array(), AWSM_JOBS_PLUGIN_VERSION, 'all' );
		wp_register_style( 'awsm-job-admin', AWSM_JOBS_PLUGIN_URL . '/assets/css/admin.min.css', array( 'wp-color-picker', 'awsm-jobs-general', 'awsm-job-admin-global' ), AWSM_JOBS_PLUGIN_VERSION, 'all' );
		wp_register_style( 'awsm-job-admin-overview', AWSM_JOBS_PLUGIN_URL . '/assets/css/admin-overview.min.css', array( 'awsm-job-admin' ), AWSM_JOBS_PLUGIN_VERSION, 'all' );

		wp_register_script( 'chartjs', AWSM_JOBS_PLUGIN_URL . '/assets/js/chart.min.js', array(), '3.6.0', true );

		wp_register_script( 'awsm-job-admin', AWSM_JOBS_PLUGIN_URL . '/assets/js/admin.min.js', $script_deps, AWSM_JOBS_PLUGIN_VERSION, true );
		wp_register_script( 'awsm-job-admin-overview', AWSM_JOBS_PLUGIN_URL . '/assets/js/admin-overview.min.js', array( 'awsm-job-admin', 'chartjs', 'postbox', 'wp-lists' ), AWSM_JOBS_PLUGIN_VERSION, true );

		wp_enqueue_style( 'awsm-job-admin-global' );
		if ( $is_job_page ) {
			wp_enqueue_style( 'awsm-jobs-general' );
			wp_enqueue_style( 'awsm-job-admin' );
			wp_enqueue_script( 'awsm-job-admin' );

			if ( $screen->id === AWSM_Job_Openings_Overview::$screen_id ) {
				wp_enqueue_style( 'awsm-job-admin-overview' );
				wp_enqueue_script( 'awsm-job-admin-overview' );
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
					'image_upload'    => array(
						'select'   => esc_html__( 'Select Image', 'wp-job-openings' ),
						'change'   => esc_html__( 'Change Image', 'wp-job-openings' ),
						'no_image' => esc_html__( 'No Image selected', 'wp-job-openings' ),
						'title'    => esc_html__( 'Select or Upload an Image', 'wp-job-openings' ),
						'btn_text' => esc_html__( 'Choose', 'wp-job-openings' ),
					),
				),
			)
		);

		wp_localize_script(
			'awsm-job-admin-overview',
			'awsmJobsAdminOverview',
			array(
				'screen_id'      => AWSM_Job_Openings_Overview::$screen_id,
				'analytics_data' => AWSM_Job_Openings_Overview::get_applications_analytics_data(),
				'i18n'           => array(
					'chart_label' => esc_html__( 'Applications', 'wp-job-openings' ),
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
		/**
		 * Filters the template path.
		 *
		 * @since 2.3.0
		 *
		 * @param string $path Template path.
		 * @param string $template_name Template name.
		 * @param string $sub_dir_name Subdirectory name.
		 */
		return apply_filters( 'awsm_jobs_template_path', $path, $template_name, $sub_dir_name );
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

	public static function is_default_pagination( $shortcode_atts = array() ) {
		$type = get_option( 'awsm_jobs_pagination_type', 'modern' );
		if ( ! empty( $shortcode_atts['pagination'] ) ) {
			$type = $shortcode_atts['pagination'];
		}
		if ( $type !== 'classic' ) {
			$type = 'modern';
		}
		return $type === 'modern';
	}

	public function awsm_jobs_content( $content ) {
		if ( ! is_singular( 'awsm_job_openings' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		ob_start();
		include self::get_template_path( 'job-content.php' );
		return ob_get_clean();
	}

	public function jobs_single_template( $single_template ) {
		global $post;
		if ( is_object( $post ) && $post->post_type === 'awsm_job_openings' ) {
			$job_details_template = get_option( 'awsm_jobs_details_page_template', 'default' );
			if ( $job_details_template === 'custom' ) {
				$single_template = self::get_template_path( 'single-job.php' );
			}
		}
		return $single_template;
	}

	public function jobs_archive_template( $archive_template ) {
		global $post;
		if ( is_object( $post ) && $post->post_type === 'awsm_job_openings' ) {
			$template_enabled = get_option( 'awsm_jobs_archive_page_template', 'plugin' );
			if ( $template_enabled === 'plugin' ) {
				$archive_template = self::get_template_path( 'archive-job.php' );
			}
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
			$date = date_i18n( get_awsm_jobs_time_format( 'application-view' ) . ', ' . get_awsm_jobs_date_format( 'application-view' ), strtotime( $post->post_date ) );
			/* translators: %s: application submission time */
			$submitted_date = sprintf( __( 'Submitted on %s', 'wp-job-openings' ), $date );
			$subtitle       = '<span class="awsm-application-submission-date">' . esc_html( $submitted_date ) . '</span>';
			$user_ip        = get_post_meta( $post->ID, 'awsm_applicant_ip', true );
			if ( ! empty( $user_ip ) ) {
				$subtitle .= ' <span class="awsm-applicant-ip">' . esc_html( __( 'from IP ', 'wp-job-openings' ) . $user_ip ) . '</span>';
			}
			echo '<p class="awsm-application-submission-info">' . $subtitle . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function awsm_jobs_taxonomies( $specs = array() ) {
		if ( empty( $specs ) ) {
			$specs = get_option( 'awsm_jobs_filter' );
		}
		if ( ! empty( $specs ) ) {
			foreach ( $specs as $spec ) {
				if ( isset( $spec['taxonomy'], $spec['filter'] ) ) {
					$taxonomy   = $spec['taxonomy'];
					$tax_length = strlen( $taxonomy );
					if ( ! taxonomy_exists( $taxonomy ) && ( $tax_length > 0 && $tax_length <= 32 ) ) {
						$args = array(
							'labels'       => array( 'name' => esc_html( $spec['filter'] ) ),
							'show_ui'      => false,
							'show_in_menu' => false,
							'query_var'    => true,
							'rewrite'      => array( 'slug' => $taxonomy ),
						);
						/**
						 * Filters the arguments for registering the job specification or taxonomy.
						 *
						 * @since 2.2.0
						 *
						 * @param array $args arguments.
						 * @param string $taxonomy The taxonomy key.
						 */
						$args = apply_filters( 'awsm_jobs_tax_args', $args, $taxonomy );
						register_taxonomy( $taxonomy, array( 'awsm_job_openings' ), $args );
					}
				}
			}
		}
	}

	public function insert_specs_terms( $specs ) {
		if ( ! empty( $specs ) ) {
			foreach ( $specs as $spec ) {
				$taxonomy = $spec['taxonomy'];
				if ( ! empty( $spec['tags'] ) ) {
					foreach ( $spec['tags'] as $spec_tag ) {
						$slug = sanitize_title( $spec_tag );
						if ( ! get_term_by( 'slug', $slug, $taxonomy ) ) {
							$args = array( 'slug' => $slug );
							wp_insert_term( $spec_tag, $taxonomy, $args );
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
		return trim( wp_strip_all_tags( $term ) );
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
							$terms      = array();
							$spec_terms = array_unique( $spec_terms );
							foreach ( $spec_terms as $spec_term ) {
								$spec_term = wp_unslash( $spec_term );
								if ( is_numeric( $spec_term ) ) {
									$term = intval( $spec_term );
									if ( ! empty( $term ) ) {
										$terms[] = $term;
									}
								} else {
									$term = $this->sanitize_term( $spec_term );
									if ( strlen( $term ) > 0 ) {
										if ( is_string( $spec_term ) && strpos( $spec_term, 'awsm-term-id-' ) !== false ) {
											$term = str_replace( 'awsm-term-id-', '', $spec_term );
										}
										$terms[] = $term;
									}
								}
							}
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
				if ( $expiration_time < ( time() - ( 24 * 60 * 60 ) ) && $post->post_status !== 'trash' ) {
					$post_data                = array();
					$post_data['ID']          = $post_id;
					$post_data['post_status'] = 'expired';
					// unhook this function so it doesn't loop infinitely
					remove_action( 'save_post', array( $this, 'awsm_job_save_post' ), 100 );
					wp_update_post( $post_data );
					// now, re-hook this function
					add_action( 'save_post', array( $this, 'awsm_job_save_post' ), 100, 2 );
				}
			} else {
				if ( $post->post_status === 'expired' ) {
					update_post_meta( $post_id, 'awsm_set_exp_list', 'set_listing' );
					update_post_meta( $post_id, 'awsm_job_expiry', gmdate( 'Y-m-d' ) );
				}
			}

			$rated_status = intval( get_option( 'awsm_jobs_plugin_rating' ) );
			if ( $rated_status !== 1 ) {
				$count      = wp_count_posts( 'awsm_job_openings' );
				$jobs_count = $count->publish + $count->expired;
				$this->enable_plugin_rating( $jobs_count );
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
			if ( ! empty( $post_parent ) ) {
				$post_type = get_post_type( $post_parent );
				if ( $post_type === 'awsm_job_openings' || $post_type === 'awsm_job_application' ) {
					$redirect = true;
					if ( $post_type === 'awsm_job_openings' ) {
						$redirect       = false;
						$attachment_url = wp_get_attachment_url( $post->ID );
						if ( ! empty( $attachment_url ) && strpos( $attachment_url, 'awsm-job-openings/' ) !== false ) {
							$redirect = true;
						}
					}
					if ( $redirect ) {
						wp_safe_redirect( esc_url( home_url( '/' ) ), 301 );
						exit;
					}
				}
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
						'field'    => 'term_id',
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

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! self::is_default_pagination( $shortcode_atts ) && ! isset( $_POST['awsm_pagination_base'] ) ) {
			// Handle classic pagination on page load.
			$paged         = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
			$args['paged'] = $paged;
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

	public static function get_job_listing_view( $shortcode_atts = array() ) {
		$view    = 'list';
		$options = get_option( 'awsm_jobs_listing_view' );
		if ( $options === 'grid-view' ) {
			$view = 'grid';
		}
		/**
		 * Filters the job listing view.
		 *
		 * @since 3.1.0
		 *
		 * @param string $view Listing view - list or grid.
		 * @param array $shortcode_atts Shortcode attributes.
		 */
		return apply_filters( 'awsm_job_listing_view', $view, $shortcode_atts );
	}

	public static function get_job_listing_view_class( $shortcode_atts = array() ) {
		$view       = self::get_job_listing_view( $shortcode_atts );
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
		/**
		 * Filters the job listing view class.
		 *
		 * @since 1.1.0
		 * @since 3.1.0 The `$shortcode_atts` parameter was added.
		 *
		 * @param string $view_class Class names.
		 * @param array $shortcode_atts The shortcode attributes.
		 */
		$view_class = apply_filters( 'awsm_job_listing_view_class', $view_class, $shortcode_atts );
		return sprintf( 'awsm-job-listings %s', $view_class );
	}

	public static function get_current_language() {
		$current_lang = null;
		// WPML and Polylang support.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'POLYLANG_VERSION' ) ) {
			$current_lang = apply_filters( 'wpml_current_language', null );
		}
		return $current_lang;
	}

	public static function set_current_language( $language ) {
		// WPML and Polylang support.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'POLYLANG_VERSION' ) ) {
			do_action( 'wpml_switch_language', $language );
		}
	}

	public static function get_job_listing_data_attrs( $shortcode_atts = array() ) {
		$attrs             = array();
		$attrs['listings'] = self::get_listings_per_page( $shortcode_atts );
		$attrs['specs']    = isset( $shortcode_atts['specs'] ) ? $shortcode_atts['specs'] : '';

		$current_lang = self::get_current_language();
		if ( ! empty( $current_lang ) ) {
			$attrs['lang'] = $current_lang;
		}

		if ( isset( $_GET['jq'] ) ) {
			$attrs['search'] = $_GET['jq'];
		}

		if ( is_tax() ) {
			$q_obj             = get_queried_object();
			$attrs['taxonomy'] = $q_obj->taxonomy;
			$attrs['term-id']  = $q_obj->term_id;
		}
		/**
		 * Filters the data attributes for the job listings div element.
		 *
		 * @since 1.1.0
		 * @since 3.1.0 The `$shortcode_atts` parameter was added.
		 *
		 * @param array $attrs The data attributes.
		 * @param array $shortcode_atts The shortcode attributes.
		 */
		return apply_filters( 'awsm_job_listing_data_attrs', $attrs, $shortcode_atts );
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
			$content = sprintf( '<div class="awsm-job-expiry-details"><span class="awsm-job-expiration-label">%1$s:</span> <span class="awsm-job-expiration-content">%2$s</span></div>', esc_html( $display_status ), esc_html( date_i18n( get_awsm_jobs_date_format( 'expiry', __( 'M j, Y', 'wp-job-openings' ) ), strtotime( $expiry_date ) ) ) );
		}
		return apply_filters( 'awsm_job_expiry_details_content', $content );
	}

	public static function get_specifications_content( $post_id, $display_label, $filter_data = array(), $listing_specs = array(), $has_term_link = true ) {
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
					/**
					 * Filter the job specification terms.
					 *
					 * @since 3.3.0
					 *
					 * @param WP_Term[]|false|WP_Error $terms Array of WP_Term objects on success.
					 * @param int $post_id The Post ID.
					 * @param string $taxonomy Taxonomy name.
					 */
					$terms = apply_filters( 'awsm_job_spec_terms', $terms, $post_id, $taxonomy );

					if ( $terms !== false && ( ! is_wp_error( $terms ) ) ) {
						$spec_label = $spec_icon = $spec_terms = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
						if ( $display_label ) {
							$spec_name  = apply_filters( 'wpml_translate_single_string', $options->label, 'WordPress', sprintf( 'taxonomy general name: %s', $options->label ) );
							$spec_label = '<span class="awsm-job-specification-label"><strong>' . $spec_name . ': </strong></span>';
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
							if ( ! is_singular( 'awsm_job_openings' ) || $is_specs_clickable !== 'make_clickable' || is_wp_error( $term_link ) || ! $has_term_link ) {
								$spec_terms .= '<span class="awsm-job-specification-term">' . esc_html( $term->name ) . '</span> ';
							} else {
								$spec_terms .= sprintf( '<a href="%2$s" class="awsm-job-specification-term">%1$s</a> ', esc_html( $term->name ), esc_url( $term_link ) );
							}
						}
						$spec_item_content = sprintf( '<div class="awsm-job-specification-item awsm-job-specification-%2$s">%1$s</div>', $spec_icon . $spec_label . $spec_terms, esc_attr( $taxonomy ) );
						/**
						 * Filters the job specification item content.
						 *
						 * @since 2.3.0
						 *
						 * @param string $spec_item_content The HTML content.
						 * @param int $post_id The Post ID.
						 * @param string $taxonomy Taxonomy name.
						 */
						$spec_item_content = apply_filters( 'awsm_job_spec_item_content', $spec_item_content, $post_id, $taxonomy );
						$spec_content     .= $spec_item_content;
					}
				}
			}
		}
		if ( ! empty( $spec_content ) ) {
			$spec_content = sprintf( '<div class="awsm-job-specification-wrapper">%1$s</div>', $spec_content );
		}

		$spec_content = apply_filters_deprecated( 'awsm_specification_content', array( $spec_content, $post_id ), '2.3.0', 'awsm_job_specs_content' );
		/**
		 * Filters the job specifications content.
		 *
		 * @since 2.3.0
		 *
		 * @param string $spec_content The HTML content.
		 * @param int $post_id The Post ID.
		 */
		return apply_filters( 'awsm_job_specs_content', $spec_content, $post_id );
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
			$data['validThrough'] = gmdate( 'c', strtotime( $expiration_date ) );
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

	/**
	 * Handle author notification emails.
	 *
	 * @since 3.4
	 *
	 */
	public function expiry_notification_handler( $new_status, $old_status, $post ) {
		$expiry_default_options = AWSM_Job_Openings_Form::get_expired_notification_content();
		$enable_expiry          = get_option( 'awsm_jobs_enable_expiry_notification', $expiry_default_options['enable'] );
		if ( $new_status !== 'publish' && $new_status !== $old_status && $post->post_type === 'awsm_job_openings' ) {
			if ( $new_status === 'expired' ) {
				if ( $enable_expiry === 'enable' ) {
					$job_id             = $post->ID;
					$admin_email        = get_option( 'admin_email' );
					$hr_mail            = get_option( 'awsm_hr_email_address' );
					$company_name       = get_option( 'awsm_job_company_name' );
					$from               = ( ! empty( $company_name ) ) ? $company_name : get_option( 'blogname' );
					$default_from_email = AWSM_Job_Openings_Settings::awsm_from_email();
					$from_email         = get_option( 'awsm_jobs_author_from_email_notification', $default_from_email );
					$to                 = get_option( 'awsm_jobs_author_to_notification', $expiry_default_options['to'] );
					$reply_to           = get_option( 'awsm_jobs_author_reply_to_notification', get_option( 'awsm_jobs_reply_to_notification' ) );
					$cc                 = get_option( 'awsm_jobs_author_hr_notification' );
					$subject            = get_option( 'awsm_jobs_author_notification_subject', $expiry_default_options['subject'] );
					$content            = get_option( 'awsm_jobs_author_notification_content', $expiry_default_options['content'] );
					$html_template      = get_option( 'awsm_jobs_notification_author_mail_template' );
					$author_id          = get_post_field( 'post_author', $job_id );
					$author_email       = get_the_author_meta( 'user_email', $author_id );
					$job_expiry         = get_post_meta( $job_id, 'awsm_job_expiry', true );

					$tags = $this->get_mail_generic_template_tags(
						array(
							'admin_email'        => $admin_email,
							'hr_email'           => $hr_mail,
							'company_name'       => $company_name,
							'job_id'             => $job_id,
							'default_from_email' => $default_from_email,
						)
					);

					if ( class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
						$awsm_filters = get_option( 'awsm_jobs_filter' );
						if ( ! empty( $awsm_filters ) ) {
							$spec_keys = wp_list_pluck( $awsm_filters, 'taxonomy' );
							foreach ( $spec_keys as $spec_key ) {
								$tag          = '{' . $spec_key . '}';
								$tags[ $tag ] = '';
								$spec_terms   = wp_get_post_terms( $job_id, $spec_key );
								if ( ! is_wp_error( $spec_terms ) && is_array( $spec_terms ) ) {
									$labels = wp_list_pluck( $spec_terms, 'name' );
									if ( ! empty( $labels ) ) {
										$tags[ $tag ] = implode( ', ', $labels ); // if there are multiple specifications, then it will be separated by a comma.
									}
								}
							}
						}
					}

					$job_title        = esc_html( get_the_title( $job_id ) );
					$tag_names        = array_keys( $tags );
					$tag_values       = array_values( $tags );
					$email_tag_names  = array( '{admin-email}', '{hr-email}', '{author-email}', '{job-id}', '{job-expiry}', '{job-title}', '{default-from-email}' );
					$email_tag_values = array( $admin_email, $hr_mail, $author_email, $job_id, $job_expiry, $job_title, $default_from_email );

					if ( ! empty( $subject ) && ! empty( $content ) ) {
						$subject    = str_replace( $tag_names, $tag_values, $subject );
						$from_email = str_replace( $email_tag_names, $email_tag_values, $from_email );
						$to         = str_replace( $email_tag_names, $email_tag_values, $to );
						$reply_to   = str_replace( $email_tag_names, $email_tag_values, $reply_to );
						$cc         = str_replace( $email_tag_names, $email_tag_values, $cc );
						$subject    = str_replace( $email_tag_names, $email_tag_values, $subject );
						$content    = str_replace( $email_tag_names, $email_tag_values, $content );

						/**
						 * Filters the author notification mail headers.
						 *
						 * @since 3.4
						 *
						 * @param array $headers Additional headers.
						 */
						$headers = apply_filters(
							'awsm_jobs_expiry_notification_mail_headers',
							array(
								'content_type' => 'Content-Type: text/html; charset=UTF-8',
								'from'         => sprintf( 'From: %1$s <%2$s>', $from, $from_email ),
								'reply_to'     => 'Reply-To: ' . $reply_to,
								'cc'           => 'Cc: ' . $cc,
							)
						);

						$reply_to = trim( str_replace( 'Reply-To:', '', $headers['reply_to'] ) );
						if ( empty( $reply_to ) ) {
							unset( $headers['reply_to'] );
						}

						$mail_cc = trim( str_replace( 'Cc:', '', $headers['cc'] ) );
						if ( empty( $mail_cc ) ) {
							unset( $headers['cc'] );
						}

						$mail_content = nl2br( AWSM_Job_Openings_Mail_Customizer::sanitize_content( $content ) );
						if ( $html_template === 'enable' ) {
							// Header mail template.
							ob_start();
							include self::get_template_path( 'header.php', 'mail' );
							$header_template  = ob_get_clean();
							$header_template .= '<div style="padding: 0 15px; font-size: 16px; max-width: 512px; margin: 0 auto;">';

							// Footer mail template.
							ob_start();
							include self::get_template_path( 'footer.php', 'mail' );
							$footer_template = ob_get_clean();
							$footer_template = '</div>' . $footer_template;

							$template = $header_template . $mail_content . $footer_template;

							/**
							 * Filters the author notification mail template.
							 *
							 * @since 3.4
							 *
							 * @param string $template Mail template.
							 * @param array $template_data Mail template data.
							 */
							$mail_content = apply_filters(
								'awsm_jobs_expiry_notification_mail_template',
								$template,
								array(
									'header' => $header_template,
									'main'   => $mail_content,
									'footer' => $footer_template,
								)
							);
						} else {
							// Basic mail template.
							ob_start();
							include self::get_template_path( 'basic.php', 'mail' );
							$basic_template = ob_get_clean();
							$mail_content   = str_replace( '{mail-content}', $mail_content, $basic_template );
						}

						$tag_names[]  = '{mail-subject}';
						$tag_values[] = $subject;
						$mail_content = str_replace( $tag_names, $tag_values, $mail_content );

						// Now, send the mail.
						$is_mail_send = wp_mail( $to, $subject, $mail_content, array_values( $headers ) );

					}
				}
			}
		}
	}

	public function display_job_post_states( $post_states, $post ) {
		if ( is_array( $post_states ) && $post->post_type === 'awsm_job_openings' && $post->post_status === 'expired' ) {
			$post_states['awsm-jobs-expired'] = sprintf( '<span class="awsm-jobs-expired-post-state">%s</span>', esc_html__( 'Expired', 'wp-job-openings' ) );
		}
		return $post_states;
	}
}

$awsm_job_openings = AWSM_Job_Openings::init();

// activation
register_activation_hook( __FILE__, array( $awsm_job_openings, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $awsm_job_openings, 'deactivate' ) );
