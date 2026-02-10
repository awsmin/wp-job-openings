<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Settings {
	private static $instance = null;

	private $permalink_msg_shown = false;

	protected $cpath = null;

	public $awsm_core = null;

	public function __construct( $awsm_core ) {
		$this->cpath     = untrailingslashit( plugin_dir_path( __FILE__ ) );
		$this->awsm_core = $awsm_core;
		$this->set_settings_capability();

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_settings_switch', array( $this, 'settings_switch_ajax' ) );
		add_action( 'before_awsm_settings_main_content', array( $this, 'settings_notice' ) );

		add_action( 'update_option_awsm_select_page_listing', array( $this, 'update_awsm_page_listing' ), 10, 2 );
		add_action( 'update_option_awsm_permalink_slug', array( $this, 'update_awsm_permalink_slug' ), 10, 2 );
		add_action( 'update_option_awsm_jobs_remove_permalink_front_base', array( $this, 'update_permalink_front_base' ), 10, 2 );
		add_action( 'update_option_awsm_jobs_disable_archive_page', array( $this, 'update_jobs_archive_page' ) );
		add_action( 'update_option_awsm_hide_uploaded_files', array( $this, 'update_awsm_hide_uploaded_files' ), 10, 2 );
		add_action( 'update_option_awsm_jobs_filter', array( $this, 'update_awsm_jobs_filter' ), 10, 2 );
		add_action( 'update_option_awsm_jobs_remove_filters', array( $this, 'update_awsm_jobs_remove_filters' ), 10, 2 );
		add_action( 'update_option_awsm_jobs_make_specs_clickable', array( $this, 'update_awsm_jobs_make_specs_clickable' ), 10, 2 );
		add_action( 'update_option_awsm_jobs_email_digest', array( $this, 'update_awsm_jobs_email_digest' ), 10, 2 );
	}

	public static function init( $awsm_core ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $awsm_core );
		}
		return self::$instance;
	}

	public function settings_page_capability( $capability ) {
		return 'manage_awsm_jobs';
	}

	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=awsm_job_openings', __( 'Settings', 'wp-job-openings' ), __( 'Settings', 'wp-job-openings' ), 'manage_awsm_jobs', 'awsm-jobs-settings', array( $this, 'settings_page' ) );
	}

	public function settings_page() {
		if ( ! current_user_can( 'manage_awsm_jobs' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-job-openings' ) );
		}
		include_once $this->cpath . '/templates/base.php';
	}

	public static function settings_tab_menus() {
		return array(
			'general'        => esc_html__( 'General', 'wp-job-openings' ),
			'appearance'     => esc_html__( 'Appearance', 'wp-job-openings' ),
			'specifications' => esc_html__( 'Job Specifications', 'wp-job-openings' ),
			'form'           => esc_html__( 'Form', 'wp-job-openings' ),
			'notification'   => esc_html__( 'Notifications', 'wp-job-openings' ),
		);
	}

	public function setting_subtabs( $section ) {
		$subtabs = array();
		switch ( $section ) {
			case 'appearance':
				$subtabs = array(
					'listing' => array(
						'id'     => 'awsm-job-listing-nav-subtab',
						'target' => 'awsm-job-listing-options-container',
						'label'  => __( 'Job Listing Page', 'wp-job-openings' ),
					),
					'details' => array(
						'id'     => 'awsm-job-details-nav-subtab',
						'target' => 'awsm-job-details-options-container',
						'label'  => __( 'Job Detail Page', 'wp-job-openings' ),
					),
				);
				break;
			case 'form':
				$subtabs = array(
					'general'   => array(
						'label' => __( 'General', 'wp-job-openings' ),
					),
					'recaptcha' => array(
						'label' => __( 'reCAPTCHA', 'wp-job-openings' ),
					),
				);
				break;
			case 'notification':
				$subtabs = array(
					'general'   => array(
						'target' => 'awsm-job-notification-options-container',
						'label'  => esc_html__( 'General', 'wp-job-openings' ),
					),
					'customize' => array(
						'label' => esc_html__( 'Customize', 'wp-job-openings' ),
					),
				);
				break;
		}
		/**
		 * Filters the Settings Subtabs.
		 *
		 * @since 1.3.0
		 *
		 * @param array $subtabs Subtabs data.
		 * @param string $section Current settings section.
		 */
		$filtered_subtabs = apply_filters( 'awsm_jobs_settings_subtabs', $subtabs, $section );

		// Compatibility fix for Pro version.
		if ( defined( 'AWSM_JOBS_PRO_PLUGIN_VERSION' ) && version_compare( AWSM_JOBS_PRO_PLUGIN_VERSION, '2.0.1', '<=' ) ) {
			$filtered_subtabs = wp_parse_args( $filtered_subtabs, $subtabs );
		}

		// Update reCAPTCHA label to CAPTCHA for new captcha version.
		if ( $section === 'form' && awsm_jobs_is_new_captcha_enabled() && isset( $filtered_subtabs['recaptcha'] ) ) {
			$filtered_subtabs['recaptcha']['label'] = __( 'CAPTCHA', 'wp-job-openings' );
		}
		return $filtered_subtabs;
	}

	private function settings() {
		$settings = array(
			'general'        => array(
				array(
					/** @since 1.3.0 */
					'option_name' => 'awsm_current_general_subtab',
				),
				array(
					'option_name' => 'awsm_select_page_listing',
				),
				array(
					'option_name' => 'awsm_job_company_name',
				),
				array(
					'option_name' => 'awsm_hr_email_address',
					'callback'    => 'sanitize_email',
				),
				array(
					/** @since 2.3.0 */
					'option_name' => 'awsm_jobs_timezone',
					'callback'    => array( $this, 'timezone_handler' ),
				),
				array(
					'option_name' => 'awsm_permalink_slug',
					'callback'    => array( $this, 'sanitize_permalink_slug' ),
				),
				array(
					/** @since 2.3.0 */
					'option_name' => 'awsm_jobs_remove_permalink_front_base',
				),
				array(
					'option_name' => 'awsm_default_msg',
					'callback'    => array( $this, 'sanitize_html_content' ),
				),
				array(
					/** @since 2.0.0 */
					'option_name' => 'awsm_jobs_email_digest',
				),
				array(
					/** @since 2.1.0 */
					'option_name' => 'awsm_jobs_disable_archive_page',
				),
				array(
					/** @since 2.1.0 */
					'option_name' => 'awsm_jobs_enable_featured_image',
				),
				array(
					/** @since 1.6.0 */
					'option_name' => 'awsm_hide_uploaded_files',
				),
				array(
					'option_name' => 'awsm_delete_data_on_uninstall',
				),
			),
			'appearance'     => array(
				array(
					'option_name' => 'awsm_current_appearance_subtab',
				),
				array(
					'option_name' => 'awsm_jobs_listing_view',
				),
				array(
					'option_name' => 'awsm_jobs_list_per_page',
					'callback'    => array( $this, 'sanitize_list_per_page' ),
				),
				array(
					'option_name' => 'awsm_jobs_number_of_columns',
					'callback'    => 'intval',
				),
				array(
					/** @since 1.6.0 */
					'option_name' => 'awsm_enable_job_search',
				),
				array(
					'option_name' => 'awsm_enable_job_filter_listing',
				),
				array(
					'option_name' => 'awsm_jobs_listing_available_filters',
					'callback'    => array( $this, 'sanitize_array_fields' ),
				),
				array(
					/** @since 3.2.0 */
					'option_name' => 'awsm_jobs_archive_page_template',
					'callback'    => array( $this, 'jobs_archive_page_template_handler' ),
				),
				array(
					'option_name' => 'awsm_jobs_listing_specs',
					'callback'    => array( $this, 'sanitize_array_fields' ),
				),
				array(
					/** @since 1.1.0 */
					'option_name' => 'awsm_jobs_details_page_template',
					'callback'    => array( $this, 'job_detail_page_template_handler' ),
				),
				array(
					'option_name' => 'awsm_jobs_details_page_layout',
				),
				array(
					/** @since 3.0.0 */
					'option_name' => 'awsm_jobs_pagination_type',
				),
				array(
					'option_name' => 'awsm_jobs_expired_jobs_listings',
				),
				array(
					/** @since 1.0.1 */
					'option_name' => 'awsm_jobs_specification_job_detail',
				),
				array(
					/** @since 1.0.1 */
					'option_name' => 'awsm_jobs_show_specs_icon',
				),
				array(
					/** @since 1.0.1 */
					'option_name' => 'awsm_jobs_make_specs_clickable',
				),
				array(
					/** @since 1.0.1 */
					'option_name' => 'awsm_jobs_specs_position',
				),
				array(
					'option_name' => 'awsm_jobs_expired_jobs_content_details',
				),
				array(
					'option_name' => 'awsm_jobs_expired_jobs_block_search',
				),
				array(
					'option_name' => 'awsm_jobs_hide_expiry_date',
				),
			),

			'specifications' => array(
				array(
					/** @since 1.3.0 */
					'option_name' => 'awsm_current_specifications_subtab',
				),
				array(
					'option_name' => 'awsm_jobs_filter',
					'callback'    => array( $this, 'awsm_jobs_filter_handle' ),
				),
				array(
					'option_name' => 'awsm_jobs_remove_filters',
					'callback'    => '',
				),
			),

			'form'           => array(
				array(
					/** @since 1.1.0 */
					'option_name' => 'awsm_current_form_subtab',
				),
				array(
					/** @since 3.1.0 */
					'option_name' => 'awsm_jobs_form_style',
				),
				array(
					/** @since 3.2.0 */
					'option_name' => 'awsm_jobs_enable_akismet_protection',
				),
				array(
					'option_name' => 'awsm_jobs_admin_upload_file_ext',
					'callback'    => array( $this, 'sanitize_upload_file_extns' ),
				),
				array(
					'option_name' => 'awsm_enable_gdpr_cb',
				),
				array(
					'option_name' => 'awsm_gdpr_cb_text',
					'callback'    => array( $this, 'awsm_gdpr_cb_text_handle' ),
				),
				array(
					/** @since 1.1.0 */
					'option_name' => 'awsm_jobs_enable_recaptcha',
				),
			),

			'notification'   => array(
				array(
					/** @since 1.3.0 */
					'option_name' => 'awsm_current_notification_subtab',
				),
				array(
					'option_name' => 'awsm_jobs_acknowledgement',
				),
				array(
					/** @since 1.5.0 */
					'option_name' => 'awsm_jobs_from_email_notification',
					'callback'    => array( $this, 'sanitize_from_email_id' ),
				),
				array(
					/** @since 1.5.0 */
					'option_name' => 'awsm_jobs_reply_to_notification',
				),
				array(
					'option_name' => 'awsm_jobs_applicant_notification',
				),
				array(
					'option_name' => 'awsm_jobs_hr_notification',
				),
				array(
					'option_name' => 'awsm_jobs_notification_subject',
				),
				array(
					'option_name' => 'awsm_jobs_notification_content',
					'callback'    => array( $this, 'applicant_notification_content_handler' ),
				),
				array(
					/** @since 2.0.0 */
					'option_name' => 'awsm_jobs_notification_mail_template',
				),
				array(
					'option_name' => 'awsm_jobs_enable_admin_notification',
				),
				array(
					/** @since 1.6.0 */
					'option_name' => 'awsm_jobs_admin_from_email_notification',
					'callback'    => array( $this, 'sanitize_from_email_id' ),
				),
				array(
					/** @since 1.6.0 */
					'option_name' => 'awsm_jobs_admin_reply_to_notification',
				),
				array(
					'option_name' => 'awsm_jobs_admin_to_notification',
				),
				array(
					'option_name' => 'awsm_jobs_admin_hr_notification',
				),
				array(
					'option_name' => 'awsm_jobs_admin_notification_subject',
				),
				array(
					'option_name' => 'awsm_jobs_admin_notification_content',
					'callback'    => array( $this, 'admin_notification_content_handler' ),
				),
				array(
					/** @since 2.0.0 */
					'option_name' => 'awsm_jobs_notification_admin_mail_template',
				),
				array(
					/** @since 2.2.0 */
					'option_name' => 'awsm_jobs_notification_customizer',
					'callback'    => array( $this, 'notification_customizer_handler' ),
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_enable_expiry_notification',
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_author_from_email_notification',
					'callback'    => array( $this, 'sanitize_from_email_id' ),
				),
				array(
					'option_name' => 'awsm_jobs_author_reply_to_notification',
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_author_to_notification',
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_author_hr_notification',
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_author_notification_subject',
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_author_notification_content',
					'callback'    => array( $this, 'author_notification_content_handler' ),
				),
				array(
					/** @since 3.4 */
					'option_name' => 'awsm_jobs_notification_author_mail_template',
				),
			),
		);
		if ( awsm_jobs_is_new_captcha_enabled() ) {
			/**
			 * @since 3.6.0 - New CAPTCHA system
			 */
			$settings['form'] = array_merge(
				$settings['form'],
				array(
					array(
						'option_name' => 'awsm_jobs_enable_captcha',
						'callback'    => array( $this, 'sanitize_captcha_enable' ),
					),
				),
				$this->get_captcha_settings_options()
			);
		} else {
			$settings['form'] = array_merge(
				$settings['form'],
				array(
					array(
						/** @since 1.1.0 */
						'option_name' => 'awsm_jobs_recaptcha_site_key',
						'callback'    => array( $this, 'sanitize_site_key' ),
					),
					array(
						/** @since 1.1.0 */
						'option_name' => 'awsm_jobs_recaptcha_secret_key',
						'callback'    => array( $this, 'sanitize_secret_key' ),
					),
				)
			);
		}
		/**
		 * Filters the settings before registration.
		 *
		 * @since 2.2.0
		 *
		 * @param array $settings Settings array.
		 */
		$settings = apply_filters( 'awsm_jobs_settings', $settings );

		return $settings;
	}

	public static function get_default_settings( $option_name = '' ) {
		$default_from_email = self::awsm_from_email( true );
		$options            = array(
			'awsm_permalink_slug'                   => 'jobs',
			'awsm_default_msg'                      => esc_html__( 'We currently have no job openings', 'wp-job-openings' ),
			'awsm_jobs_listing_view'                => 'list-view',
			'awsm_jobs_list_per_page'               => 10,
			'awsm_jobs_number_of_columns'           => 3,
			'awsm_current_appearance_subtab'        => 'awsm-job-listing-nav-subtab',
			'awsm_jobs_details_page_layout'         => 'single',
			'awsm_jobs_filter'                      => array(
				array(
					'taxonomy' => 'job-category',
					'filter'   => esc_html__( 'Job Category', 'wp-job-openings' ),
				),
				array(
					'taxonomy' => 'job-type',
					'filter'   => esc_html__( 'Job Type', 'wp-job-openings' ),
					'tags'     => array( 'Full Time', 'Part Time', 'Freelance' ),
				),
				array(
					'taxonomy' => 'job-location',
					'filter'   => esc_html__( 'Job Location', 'wp-job-openings' ),
				),
			),
			'awsm_enable_job_filter_listing'        => 'enabled',
			'awsm_jobs_listing_available_filters'   => array( 'job-category', 'job-type', 'job-location' ),
			'awsm_jobs_listing_specs'               => array( 'job-category', 'job-location' ),
			'awsm_jobs_admin_upload_file_ext'       => array( 'pdf', 'doc', 'docx' ),
			'awsm_enable_gdpr_cb'                   => 'true',
			'awsm_gdpr_cb_text'                     => esc_html__( 'By using this form you agree with the storage and handling of your data by this website.', 'wp-job-openings' ),
			'awsm_jobs_acknowledgement'             => 'acknowledgement',
			'awsm_jobs_notification_subject'        => 'Thanks for submitting your application for a job at {company}',
			'awsm_jobs_notification_content'        => "Dear {applicant},\n\nThis is to let you know that we have received your application.We appreciate your interest in {company} and the position of {job-title} for which you applied.  If you are selected for an interview, you can expect a phone call from our Human Resources staff shortly.\n\n Thank you, again, for your interest in our company. We do appreciate the time that you invested in this application.\n\nSincerely\n\nHR Manager\n{company}",
			'awsm_jobs_enable_admin_notification'   => 'enable',
			'awsm_jobs_admin_notification_subject'  => 'New application received for the position {job-title} [{job-id}]',
			'awsm_jobs_admin_notification_content'  => "Job Opening: {job-title} [{job-id}]\nName: {applicant}\nEmail: {applicant-email}\nPhone: {applicant-phone}\nResume: {applicant-resume}\nCover letter: {applicant-cover}\n\nPowered by WP Job Openings Plugin",
			'awsm_jobs_enable_expiry_notification'  => 'enable',
			'awsm_jobs_author_notification_subject' => 'Job Listing Expired',
			'awsm_jobs_author_notification_content' => "This email is to notify you that your job listing for [{job-title}] has just expired. As a result, applicants will no longer be able to apply for this position.\n\nIf you would like to extend the expiration date or remove the listing, please log in to the dashboard and take the necessary steps.\n\nPowered by WP Job Openings Plugin",
			'awsm_jobs_notification_customizer'     => array(
				'logo'        => 'default',
				'base_color'  => '#05BC9C',
				'from_email'  => $default_from_email,
				/* translators: %1$s: Site link, %2$s: Plugin website link */
				'footer_text' => sprintf( esc_html__( 'Sent from %1$s by %2$s Plugin', 'wp-job-openings' ), '<a href="{site-url}">{site-title}</a>', '<a href="https://wpjobopenings.com">' . esc_html__( 'WP Job Openings', 'wp-job-openings' ) . '</a>' ),
			),
			'awsm_jobs_email_digest'                => 'enable',
		);
		if ( ! empty( $option_name ) ) {
			if ( isset( $options[ $option_name ] ) ) {
				return $options[ $option_name ];
			} else {
				return '';
			}
		} else {
			return $options;
		}
	}

	private static function default_settings() {
		$options = self::get_default_settings();
		foreach ( $options as $option => $value ) {
			if ( ! get_option( $option ) ) {
				update_option( $option, $value );
			}
		}
	}

	public static function register_defaults() {
		if ( get_option( 'awsm_register_default_settings' ) == 1 ) {
			return;
		}
		self::default_settings();
		update_option( 'awsm_register_default_settings', 1 );
	}

	public function register_settings() {
		$settings = $this->settings();
		foreach ( $settings as $group => $settings_args ) {
			foreach ( $settings_args as $setting_args ) {
				register_setting( 'awsm-jobs-' . $group . '-settings', $setting_args['option_name'], isset( $setting_args['callback'] ) ? $setting_args['callback'] : 'sanitize_text_field' );
			}
		}
	}

	private function set_settings_capability() {
		$settings = $this->settings();
		foreach ( $settings as $group => $settings_args ) {
			add_filter( 'option_page_capability_awsm-jobs-' . $group . '-settings', array( $this, 'settings_page_capability' ), 11 );
		}
	}

	public function file_upload_extensions() {
		$extns = array( 'pdf', 'doc', 'docx', 'rtf' );
		return apply_filters( 'awsm_jobs_form_file_extensions', $extns );
	}

	public function sanitize_permalink_slug( $input ) {
		$old_value = get_option( 'awsm_permalink_slug' );
		if ( empty( $input ) ) {
			add_settings_error( 'awsm_permalink_slug', 'awsm-permalink-slug', esc_html__( 'URL slug cannot be empty.', 'wp-job-openings' ) );
			$input = $old_value;
		}
		$slug = sanitize_title( $input, 'jobs' );
		$page = get_page_by_path( $slug, ARRAY_N );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$disable_archive = isset( $_POST['awsm_jobs_disable_archive_page'] ) ? sanitize_text_field( $_POST['awsm_jobs_disable_archive_page'] ) : '';
		if ( is_array( $page ) && $disable_archive !== 'disable' ) {
			$msg = __( 'The slug cannot be updated.', 'wp-job-openings' );
			if ( $slug === $old_value ) {
				$msg = __( 'The URL slug is not valid.', 'wp-job-openings' );
			}
			add_settings_error( 'awsm_permalink_slug', 'awsm-permalink-slug', esc_html( $msg . ' ' . __( 'A page with the same slug exists. Please choose a different URL slug or disable the archive page for Job Openings and try again!', 'wp-job-openings' ) ) );
			$slug = $old_value;
		}
		return $slug;
	}

	public function sanitize_site_key( $input ) {
		$old_value = get_option( 'awsm_jobs_recaptcha_site_key' );
		$enable    = get_option( 'awsm_jobs_enable_recaptcha' );
		if ( empty( $input ) && $enable === 'enable' ) {
			add_settings_error( 'awsm_jobs_recaptcha_site_key', 'awsm-recaptcha-site-key', esc_html__( 'Invalid site key provided.', 'wp-job-openings' ) );
			$input = $old_value;
		}
		return $input;
	}

	public function sanitize_secret_key( $input ) {
		$old_value = get_option( 'awsm_jobs_recaptcha_secret_key' );
		$enable    = get_option( 'awsm_jobs_enable_recaptcha' );
		if ( empty( $input ) && $enable === 'enable' ) {
			add_settings_error( 'awsm_jobs_recaptcha_secret_key', 'awsm-recaptcha-secret-key', esc_html__( 'Invalid secret key provided.', 'wp-job-openings' ) );
			$input = $old_value;
		}
		return $input;
	}

	public function is_localhost() {
		$server_name = strtolower( $_SERVER['SERVER_NAME'] );
		return in_array( $server_name, array( 'localhost', '127.0.0.1' ) );
	}

	public function is_email_in_domain( $email, $domain ) {
		$match        = false;
		$domain       = strtolower( $domain );
		$email_domain = substr( $email, strrpos( $email, '@' ) + 1 );
		$email_domain = strtolower( $email_domain );
		$domain_parts = explode( '.', $domain );
		do {
			$site_domain = implode( '.', $domain_parts );
			if ( $site_domain === $email_domain ) {
				$match = true;
				break;
			}
			array_shift( $domain_parts );
		} while ( $domain_parts );
		return $match;
	}

	public function validate_from_email_id( $email ) {
		$site_domain = strtolower( $_SERVER['SERVER_NAME'] );
		if ( $this->is_localhost() ) {
			return $email;
		}

		if ( preg_match( '/^[0-9.]+$/', $site_domain ) ) {
			return $email;
		}

		if ( trim( $email ) === '{default-from-email}' ) {
			return $email;
		}

		if ( $this->is_email_in_domain( $email, $site_domain ) ) {
			return $email;
		}

		$home_url = home_url();
		if ( preg_match( '%^https?://([^/]+)%', $home_url, $matches ) ) {
			$current_domain = strtolower( $matches[1] );

			if ( $current_domain !== $site_domain && $this->is_email_in_domain( $email, $current_domain ) ) {
				return $email;
			} else {
				return false;
			}
		}
		return false;
	}

	public function sanitize_from_email_id( $email ) {
		if ( empty( $email ) ) {
			$email = $this->awsm_from_email( true );
		}
		return sanitize_text_field( $email );
	}

	public function sanitize_list_per_page( $input ) {
		$number_of_columns = intval( $input );
		if ( $number_of_columns < 1 ) {
			add_settings_error( 'awsm_jobs_list_per_page', 'awsm-list-per-page', esc_html__( 'Listings per page must be greater than or equal to 1.', 'wp-job-openings' ) );
			return false;
		}
		return $number_of_columns;
	}

	public function sanitize_array_fields( $input ) {
		if ( is_array( $input ) ) {
			$input = array_map( 'sanitize_text_field', $input );
		}
		return $input;
	}

	public function jobs_archive_page_template_handler( $input ) {
		return $this->template_handler( 'awsm_jobs_archive_page_template', $input );
	}

	public function job_detail_page_template_handler( $input ) {
		return $this->template_handler( 'awsm_jobs_details_page_template', $input );
	}

	public function template_handler( $option_name, $template ) {
		$template = sanitize_text_field( $template );
		if ( ( $template === 'custom' || $template === 'plugin' ) && function_exists( 'wp_is_block_theme' ) ) {
			if ( wp_is_block_theme() ) {
				$prefix = str_replace( array( 'awsm_jobs_', '_' ), array( '', ' ' ), $option_name );
				add_settings_error( $option_name, str_replace( '_', '-', $option_name ), ucwords( $prefix ) . ': ' . esc_html__( 'Block theme detected! It is recommended to use a theme template instead of plugin generated template.', 'wp-job-openings' ), 'awsm-jobs-warning' );
			}
		}
		return $template;
	}

	public function awsm_jobs_filter_handle( $filters ) {
		$old_value = get_option( 'awsm_jobs_filter' );
		if ( ! empty( $filters ) ) {
			foreach ( $filters as $index => $filter ) {
				$spec_name = isset( $filter['filter'] ) ? sanitize_text_field( $filter['filter'] ) : '';
				$spec_key  = isset( $filter['taxonomy'] ) ? sanitize_title_with_dashes( $filter['taxonomy'] ) : '';

				// Job specifications validation.
				if ( empty( $spec_name ) || empty( $spec_key ) ) {
					add_settings_error( 'awsm_jobs_filter', 'awsm-jobs-filter', esc_html__( 'Job Specification and Key cannot be empty!', 'wp-job-openings' ) );
					return $old_value;
				}
				if ( strlen( $spec_key ) > 32 ) {
					add_settings_error( 'awsm_jobs_filter', 'awsm-jobs-filter', esc_html__( 'Job specification key must not exceed 32 characters.', 'wp-job-openings' ) );
					return $old_value;
				}
				if ( ! preg_match( '/^([a-z0-9]+(-|_))*[a-z0-9]+$/', $spec_key ) ) {
					add_settings_error( 'awsm_jobs_filter', 'awsm-jobs-filter', esc_html__( 'The job specification key should only contain alphanumeric, latin characters separated by hyphen/underscore, and cannot begin or end with a hyphen/underscore.', 'wp-job-openings' ) );
					return $old_value;
				}
				if ( isset( $filter['register'] ) ) {
					if ( taxonomy_exists( $spec_key ) ) {
						/* translators: %1$s: job specification key, %2$s: specific error message */
						add_settings_error( 'awsm_jobs_filter', 'awsm-jobs-filter', sprintf( esc_html__( 'Error in registering Job Specification with key: %1$s. %2$s', 'wp-job-openings' ), '<em>' . $spec_key . '</em>', esc_html__( 'Taxonomy already exist!', 'wp-job-openings' ) ) );
						unset( $filters[ $index ] );
						continue;
					}
				}

				$filters[ $index ]['filter']   = $spec_name;
				$filters[ $index ]['taxonomy'] = $spec_key;
				if ( isset( $filter['remove_tags'] ) ) {
					if ( ! empty( $filter['remove_tags'] ) ) {
						$remove_tags = $filter['remove_tags'];
						foreach ( $remove_tags as $remove_tag ) {
							$term = get_term_by( 'id', $remove_tag, $spec_key );
							if ( $term instanceof \WP_Term ) {
								wp_delete_term( $term->term_id, $spec_key );
							}
						}
					}
				}
				if ( isset( $filter['icon'] ) ) {
					if ( ! empty( $filter['icon'] ) ) {
						$filters[ $index ]['icon'] = sanitize_text_field( $filter['icon'] );
					}
				}
				if ( isset( $filter['tags'] ) ) {
					if ( ! empty( $filter['tags'] ) ) {
						$filters[ $index ]['tags'] = array_map( array( AWSM_Job_Openings::init(), 'sanitize_term' ), $filter['tags'] );
					}
				}
			}
		}
		return $filters;
	}

	public function update_awsm_jobs_filter( $old_value, $new_value ) {
		$awsm_job_openings = AWSM_Job_Openings::init();
		$awsm_job_openings->awsm_jobs_taxonomies( $new_value );
		$awsm_job_openings->insert_specs_terms( $new_value );
	}

	public function update_awsm_jobs_remove_filters( $old_value, $new_value ) {
		$filters = $new_value;
		if ( ! empty( $filters ) ) {
			$taxonomy_objects = get_object_taxonomies( 'awsm_job_openings' );
			foreach ( $filters as $filter ) {
				if ( taxonomy_exists( $filter ) && in_array( $filter, $taxonomy_objects ) ) {
					$terms = get_terms(
						array(
							'taxonomy'   => $filter,
							'orderby'    => 'id',
							'hide_empty' => false,
						)
					);
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							wp_delete_term( $term->term_id, $filter );
						}
					}
				}
			}
		}
	}

	public function sanitize_upload_file_extns( $input ) {
		$valid     = true;
		$all_extns = $this->file_upload_extensions();
		if ( is_array( $input ) ) {
			foreach ( $input as $ext ) {
				if ( ! in_array( $ext, $all_extns, true ) ) {
					$valid = false;
					break;
				}
			}
		}
		$default_extns = array( 'pdf', 'doc', 'docx' );
		if ( empty( $input ) ) {
			return $default_extns;
		}
		if ( ! $valid ) {
			add_settings_error( 'awsm_jobs_admin_upload_file_ext', 'awsm-upload-file-extension', esc_html__( 'Error in saving file upload types!', 'wp-job-openings' ) );
			return $default_extns;
		}
		return array_map( 'sanitize_text_field', $input );
	}

	public function sanitize_html_content( $content ) {
		if ( ! class_exists( 'AWSM_Job_Openings_Form' ) ) {
			require_once AWSM_JOBS_PLUGIN_DIR . '/inc/class-awsm-job-openings-form.php';
		}
		return wp_kses( $content, AWSM_Job_Openings_Form::get_allowed_html() );
	}

	public function awsm_gdpr_cb_text_handle( $input ) {
		$gdpr_enable = get_option( 'awsm_enable_gdpr_cb' );
		if ( ! empty( $gdpr_enable ) && empty( $input ) ) {
			$input = esc_html__( 'By using this form you agree with the storage and handling of your data by this website.', 'wp-job-openings' );
		}
		return $this->sanitize_html_content( $input );
	}

	public function notification_content_handler( $input, $option_name ) {
		$content = trim( $input );
		if ( empty( $content ) ) {
			add_settings_error( $option_name, str_replace( '_', '-', $option_name ), esc_html__( 'Notification content cannot be empty.', 'wp-job-openings' ) );
			$input = self::get_default_settings( $option_name );
		}
		return wp_kses_post( $input );
	}

	public function applicant_notification_content_handler( $input ) {
		return $this->notification_content_handler( $input, 'awsm_jobs_notification_content' );
	}

	public function admin_notification_content_handler( $input ) {
		return $this->notification_content_handler( $input, 'awsm_jobs_admin_notification_content' );
	}

	public function author_notification_content_handler( $input ) {
		return $this->notification_content_handler( $input, 'awsm_jobs_author_notification_content' );
	}

	public function notification_customizer_handler( $input ) {
		$customizer_settings = AWSM_Job_Openings_Mail_Customizer::get_settings();
		if ( empty( $input ) || ! is_array( $input ) ) {
			$input = $customizer_settings;
		}
		$input['logo']        = sanitize_text_field( $input['logo'] );
		$input['from_email']  = $this->sanitize_from_email_id( $input['from_email'] );
		$input['base_color']  = sanitize_text_field( $input['base_color'] );
		$input['footer_text'] = AWSM_Job_Openings_Mail_Customizer::sanitize_content( $input['footer_text'] );
		return $input;
	}

	public function timezone_handler( $timezone ) {
		$options = array(
			'timezone_string' => '',
			'gmt_offset'      => '',
		);
		if ( ! empty( $timezone ) && isset( $timezone['original_val'] ) ) {
			if ( preg_match( '/^UTC[+-]/', $timezone['original_val'] ) ) {
				$options['gmt_offset'] = preg_replace( '/UTC\+?/', '', $timezone['original_val'] );
			} else {
				$options['timezone_string'] = $timezone['original_val'];
			}
		}
		return wp_parse_args( $timezone, $options );
	}

	public function update_awsm_page_listing( $old_value, $value ) {
		$page_id = $value;
		if ( ! empty( $page_id ) ) {
			AWSM_Job_Openings::add_shortcode_to_page( $page_id );
		}
	}

	public function refresh_permalink( $option_name ) {
		$this->awsm_core->unregister_awsm_job_openings_post_type();
		$this->awsm_core->register_post_types();
		flush_rewrite_rules();

		if ( ! $this->permalink_msg_shown ) {
			$this->permalink_msg_shown = true;
			/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
			add_settings_error( $option_name, str_replace( '_', '-', $option_name ), sprintf( esc_html__( 'Please refresh the %1$sPermalink Settings%2$s to reflect the changes.', 'wp-job-openings' ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">', '</a>' ), 'awsm-jobs-warning' );
		}
	}

	public function update_awsm_permalink_slug( $old_value, $value ) {
		if ( empty( $value ) ) {
			update_option( 'awsm_permalink_slug', 'jobs' );
		}
		$this->refresh_permalink( 'awsm_permalink_slug' );
	}

	public function update_permalink_front_base() {
		$this->refresh_permalink( 'awsm_jobs_remove_permalink_front_base' );
	}

	public function update_jobs_archive_page() {
		$this->refresh_permalink( 'awsm_jobs_disable_archive_page' );
	}

	public function update_awsm_hide_uploaded_files( $old_value, $value ) {
		$upload_dir   = wp_upload_dir();
		$base_dir     = trailingslashit( $upload_dir['basedir'] );
		$upload_dir   = $base_dir . AWSM_JOBS_UPLOAD_DIR_NAME;
		$file_name    = trailingslashit( $upload_dir ) . '.htaccess';
		$file_content = 'Options -Indexes';
		if ( $value === 'hide_files' ) {
			$file_content = 'deny from all';
		}
		$handle = @fopen( $file_name, 'w' );
		if ( $handle ) {
			fwrite( $handle, $file_content );
			fclose( $handle );
		}
	}

	public function update_awsm_jobs_make_specs_clickable( $old_value, $value ) {
		if ( ! empty( $value ) ) {
			flush_rewrite_rules();
		}
	}

	public function update_awsm_jobs_email_digest( $old_value, $value ) {
		if ( empty( $value ) ) {
			wp_clear_scheduled_hook( 'awsm_jobs_email_digest' );
		}
	}

	public function settings_switch_ajax() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'awsm-admin-nonce' ) ) {
			wp_die();
		}
		if ( ! current_user_can( 'manage_awsm_jobs' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to manage options.', 'wp-job-openings' ) );
		}
		if ( isset( $_POST['option'], $_POST['option_value'] ) ) {
			/**
			 * Filters the allowed options for switchable settings.
			 *
			 * @since 1.4
			 *
			 * @param array $allowed_options Allowed options.
			 */
			$allowed_options = apply_filters( 'awsm_jobs_switchable_settings_options', array( 'awsm_jobs_acknowledgement', 'awsm_jobs_enable_admin_notification' ) );
			$option          = sanitize_text_field( $_POST['option'] );
			$option_value    = sanitize_text_field( $_POST['option_value'] );
			if ( ! empty( $option ) ) {
				if ( in_array( $option, $allowed_options, true ) ) {
					update_option( $option, $option_value );
				} else {
					/* translators: %s: option name */
					wp_die( sprintf( esc_html__( "Error in updating option: '%s'", 'wp-job-openings' ), esc_html( $option ) ) );
				}
			}
			echo $option_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		wp_die();
	}

	public function is_settings_field_checked( $option, $value, $default = false ) {
		$checked = '';
		if ( ! empty( $option ) ) {
			if ( $option === $value ) {
				$checked = 'checked';
			}
		} else {
			if ( $default ) {
				$checked = 'checked';
			}
		}
		return $checked;
	}

	public function display_subtabs( $section, $subtab_id = '' ) {
		$subtabs = $this->setting_subtabs( $section );
		if ( ! empty( $subtabs ) ) :
			$initial_tab        = true;
			$current_tab_option = "awsm_current_{$section}_subtab";
			$current_tab_id     = get_option( $current_tab_option, $subtab_id );
			?>
			<div class="awsm-nav-subtab-container awsm-clearfix">
				<ul class="subsubsub">
					<?php
					foreach ( $subtabs as $key => $subtab ) :
						$unique_id     = $key . '-' . $section;
						$id            = isset( $subtab['id'] ) ? $subtab['id'] : "awsm-{$unique_id}-nav-subtab";
						$target        = isset( $subtab['target'] ) ? $subtab['target'] : "awsm-{$unique_id}-options-container";
						$current_class = $initial_tab ? ' current' : '';
						?>
							<li>
								<a href="#" class="awsm-nav-subtab<?php echo esc_attr( $current_class ); ?>" id="<?php echo esc_attr( $id ); ?>" data-target="#<?php echo esc_attr( $target ); ?>">
								<?php echo esc_html( $subtab['label'] ); ?>
								</a>
							</li>
						<?php
						$initial_tab = false;
						endforeach;
					?>
					<?php do_action( 'awsm_jobs_settings_subtab_section', $section ); ?>
				</ul>
				<input type="hidden" name="<?php echo esc_attr( $current_tab_option ); ?>" class="awsm_current_settings_subtab" value="<?php echo esc_attr( $current_tab_id ); ?>" />
			</div>
			<?php
		endif;
	}

	public function settings_notice( $setting_slug ) {
		?>
			<div class="awsm-jobs-settings-error awsm-jobs-error-container awsm-hide">
				<div class="awsm-jobs-error">
					<p>
						<strong>
						<?php
							/* translators: %s Settings slug */
							printf( esc_html__( 'Error: Invalid %s settings. Please make sure that all the required fields are filled and valid, then submit the form.', 'wp-job-openings' ), esc_html( $setting_slug ) );
						?>
						</strong>
					</p>
				</div>
			</div>
		<?php
	}

	public function display_settings_fields( $settings_fields, $container = 'table', $echo = true ) {
		$content = '';
		if ( ! empty( $settings_fields ) && is_array( $settings_fields ) ) {
			$allowed_html = array(
				'br'     => array(),
				'em'     => array(),
				'span'   => array(),
				'strong' => array(),
				'small'  => array(),
				'p'      => array( 'class' => true ),
			);
			foreach ( $settings_fields as $field_details ) {
				if ( isset( $field_details['visible'] ) && $field_details['visible'] === false ) {
					continue;
				}

				$field_name = isset( $field_details['name'] ) ? $field_details['name'] : '';
				$id         = isset( $field_details['id'] ) ? $field_details['id'] : $field_name;
				// name or id must exist, else continue to next iteration.
				if ( empty( $field_name ) && empty( $id ) ) {
					continue;
				}

				$field_type    = isset( $field_details['type'] ) ? $field_details['type'] : 'text';
				$label         = isset( $field_details['label'] ) ? $field_details['label'] : '';
				$field_label   = '';
				$field_content = '';

				if ( $field_type !== 'title' ) {
					$class_name = isset( $field_details['class'] ) ? $field_details['class'] : '';
					if ( $field_type === 'textarea' ) {
						$class_name = trim( 'large-text ' . $class_name );
					} elseif ( $field_type === 'colorpicker' ) {
						$class_name = trim( 'awsm-jobs-colorpicker-field ' . $class_name );
					} elseif ( $field_type === 'image' ) {
						$class_name = trim( 'awsm-settings-image-field-container ' . $class_name );
					} else {
						if ( ! isset( $field_details['class'] ) && $field_type !== 'checkbox' && $field_type !== 'radio' ) {
							$class_name = 'regular-text';
						}
					}
					$class_attr  = ! empty( $class_name ) ? sprintf( ' class="%s"', esc_attr( $class_name ) ) : '';
					$value       = isset( $field_details['value'] ) ? $field_details['value'] : '';
					$description = isset( $field_details['description'] ) ? $field_details['description'] : '';
					$help_button = isset( $field_details['help_button'] ) && is_array( $field_details['help_button'] ) ? $field_details['help_button'] : '';

					if ( $field_type === 'checkbox' ) {
						$field_label = esc_html( $label );
					} else {
						$field_label = sprintf( '<label for="%2$s">%1$s</label>', esc_html( $label ), esc_attr( $id ) );
					}
					if ( $field_type === 'raw' ) {
						$field_content = $value;
					} else {
						if ( isset( $field_details['name'] ) && ! isset( $field_details['value'] ) ) {
							$default_value = isset( $field_details['default_value'] ) ? $field_details['default_value'] : '';
							$value         = get_option( $field_name, $default_value );
						}
						$multiple    = isset( $field_details['multiple'] ) ? $field_details['multiple'] : false;
						$extra_attrs = $class_attr;
						if ( isset( $field_details['required'] ) && $field_details['required'] === true ) {
							$extra_attrs .= ' required';
						}
						if ( isset( $field_details['other_attrs'] ) && is_array( $field_details['other_attrs'] ) ) {
							$other_attrs = $field_details['other_attrs'];
							foreach ( $other_attrs as $other_attr => $other_attr_val ) {
								$extra_attrs .= sprintf( ' %s="%s"', esc_attr( $other_attr ), esc_attr( $other_attr_val ) );
							}
						}
						if ( isset( $field_details['choices'] ) && is_array( $field_details['choices'] ) ) {
							$choices       = $field_details['choices'];
							$choices_count = count( $choices );
							if ( $field_type === 'checkbox' && $choices_count > 0 && $multiple ) {
								$field_name .= '[]';
							}
							$choice_fields = 1;
							foreach ( $choices as $choice_details ) {
								$choice_attrs = $field_type !== 'select' ? $extra_attrs : '';
								$choice       = isset( $choice_details['value'] ) ? $choice_details['value'] : '';
								$choice_text  = isset( $choice_details['text'] ) ? $choice_details['text'] : '';
								if ( isset( $choice_details['data_attrs'] ) && is_array( $choice_details['data_attrs'] ) ) {
									$choice_data_attrs = $choice_details['data_attrs'];
									foreach ( $choice_data_attrs as $choice_data_attr ) {
										$choice_attrs .= sprintf( ' data-%1$s="%2$s"', esc_attr( $choice_data_attr['attr'] ), esc_attr( $choice_data_attr['value'] ) );
									}
								}
								if ( $field_type === 'checkbox' || $field_type === 'radio' ) {
									$choice_id         = isset( $choice_details['id'] ) ? $choice_details['id'] : ( $choices_count > 1 ? $id . '-' . $choice_fields : $id );
									$choice_text_class = isset( $choice_details['text_class'] ) ? $choice_details['text_class'] : '';
									if ( $field_type === 'checkbox' && isset( $choice_details['name'] ) ) {
										$field_name = $choice_details['name'];
										if ( ! isset( $choice_details['id'] ) ) {
											$choice_id = $field_name;
										}
										if ( isset( $choice_details['checked_value'] ) ) {
											$value = $choice_details['checked_value'];
										} else {
											$default_value = isset( $choice_details['default_value'] ) ? $choice_details['default_value'] : '';
											$value         = get_option( $field_name, $default_value );
										}
									}
									if ( is_array( $value ) ) {
										$choice_attrs .= ' ' . checked( in_array( $choice, $value ), true, false );
									} else {
										$choice_attrs .= ' ' . checked( $value, $choice, false );
									}
									$text_extra_attrs     = ! empty( $choice_text_class ) ? sprintf( ' class="%s"', esc_attr( $choice_text_class ) ) : '';
									$choice_field         = sprintf( '<input type="%1$s" name="%2$s" id="%3$s" value="%4$s"%5$s />', esc_attr( $field_type ), esc_attr( $field_name ), esc_attr( $choice_id ), esc_attr( $choice ), $choice_attrs );
									$choice_field_content = sprintf( '<label for="%2$s"%3$s>%1$s</label>', $choice_field . esc_html( $choice_text ), esc_attr( $choice_id ), $text_extra_attrs );

									if ( $field_type === 'radio' || ( $field_type === 'checkbox' && $multiple === true ) ) {
										$field_content .= sprintf( '<li>%s</li>', $choice_field_content );
									} else {
										$field_content .= $choice_field_content;
									}
									$choice_fields++;
								} elseif ( $field_type === 'select' ) {
									$choice_attrs  .= ' ' . selected( $value, $choice, false );
									$field_content .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $choice ), esc_html( $choice_text ), $choice_attrs );
								}
							}
							if ( $field_type === 'radio' || ( $field_type === 'checkbox' && $multiple === true ) ) {
								$list_class = ( $field_type === 'checkbox' && $multiple === true ) ? 'awsm-check-list' : 'awsm-list-inline';
								if ( isset( $field_details['list_class'] ) ) {
									$list_class = $field_details['list_class'];
								}
								$field_content = sprintf( '<ul class="%2$s">%1$s</ul>', $field_content, esc_attr( $list_class ) );
							} elseif ( $field_type === 'select' ) {
								$field_content = sprintf( '<select name="%2$s" id="%3$s"%4$s>%1$s</select>', $field_content, esc_attr( $field_name ), esc_attr( $id ), $extra_attrs );
							}
						} else {
							if ( $field_type === 'textarea' ) {
								$field_content = sprintf( '<textarea name="%1$s" id="%2$s"%4$s>%3$s</textarea>', esc_attr( $field_name ), esc_attr( $id ), esc_attr( $value ), $extra_attrs );
							} elseif ( $field_type === 'editor' ) {
								ob_start();
								$editor_settings = array(
									'textarea_name' => $field_name,
								);
								if ( isset( $field_details['other_attrs'] ) && is_array( $field_details['other_attrs'] ) ) {
									$editor_settings = array_merge( $editor_settings, $field_details['other_attrs'] );
								}
								awsm_jobs_wp_editor( $value, $id, $editor_settings );
								$field_content = ob_get_clean();
							} elseif ( $field_type === 'image' ) {
								$image_url = '';
								if ( ! empty( $value ) ) {
									if ( $value === 'default' ) {
										$image_url = AWSM_Job_Openings_Mail_Customizer::get_default_logo();
									} else {
										$image_url = awsm_jobs_get_original_image_url( $value );
									}
								}

								$image_container = '';
								$image_button    = '';
								if ( empty( $image_url ) ) {
									$image_container = sprintf( '<div class="awsm-settings-image awsm-settings-no-image"><span>%s</span></div>', esc_html__( 'No Image selected', 'wp-job-openings' ) );
									$image_button    = sprintf( '<button type="button" class="button awsm-settings-image-upload-button">%1$s</button><button type="button" class="awsm-hidden-control button awsm-settings-image-remove-button">%2$s</button>', esc_html__( 'Select Image', 'wp-job-openings' ), __( 'Remove', 'wp-job-openings' ) );
								} else {
									$image_container = sprintf( '<div class="awsm-settings-image"><img src="%s" /></div>', esc_url( $image_url ) );
									$image_button    = sprintf( '<button type="button" class="button awsm-settings-image-upload-button">%1$s</button><button type="button" class="button awsm-settings-image-remove-button">%2$s</button>', esc_html__( 'Change Image', 'wp-job-openings' ), __( 'Remove', 'wp-job-openings' ) );
								}
								$field_content = sprintf( '<div id="%4$s"%5$s>%3$s<input type="hidden" name="%1$s" class="awsm-settings-image-field" value="%2$s" /></div>', esc_attr( $field_name ), esc_attr( $value ), $image_container . $image_button, esc_attr( $id ), $extra_attrs );
							} else {
								$field_content = sprintf( '<input type="%1$s" name="%2$s" id="%3$s" value="%4$s"%5$s />', esc_attr( $field_type ), esc_attr( $field_name ), esc_attr( $id ), esc_attr( $value ), $extra_attrs );
							}
						}
					}
					if ( ! empty( $help_button ) ) {
						$btn_visible = isset( $help_button['visible'] ) ? $help_button['visible'] : true;
						if ( $btn_visible ) {
							$btn_url    = isset( $help_button['url'] ) ? $help_button['url'] : '';
							$btn_class  = 'button';
							$btn_class .= isset( $help_button['class'] ) ? ' ' . $help_button['class'] : '';
							$btn_text   = isset( $help_button['text'] ) ? $help_button['text'] : '';
							$btn_extras = '';
							if ( isset( $help_button['other_attrs'] ) && is_array( $help_button['other_attrs'] ) ) {
								$btn_other_attrs = $help_button['other_attrs'];
								foreach ( $btn_other_attrs as $btn_other_attr => $btn_other_attr_val ) {
									$btn_extras .= sprintf( ' %s="%s"', esc_attr( $btn_other_attr ), esc_attr( $btn_other_attr_val ) );
								}
							}
							$field_content .= sprintf( ' <a href="%2$s" class="%3$s"%4$s>%1$s</a>', esc_html( $btn_text ), esc_url( $btn_url ), esc_attr( $btn_class ), $btn_extras );
						}
					}
					if ( ! empty( $description ) ) {
						$field_content .= sprintf( '<p class="description">%s</p>', wp_kses( $description, $allowed_html ) );
					}
				}

				$container_attrs = '';
				if ( isset( $field_details['container_class'] ) && ! empty( $field_details['container_class'] ) ) {
					$container_attrs = sprintf( ' class="%s"', esc_attr( $field_details['container_class'] ) );
				}
				if ( isset( $field_details['container_id'] ) && ! empty( $field_details['container_id'] ) ) {
					$container_attrs .= sprintf( ' id="%s"', esc_attr( $field_details['container_id'] ) );
				}
				if ( $container === 'table' ) {
					if ( $field_type === 'title' ) {
						$content .= sprintf( '<tr%3$s><th scope="row" colspan="2" class="awsm-form-head-title"><h2 id="%2$s">%1$s</h2></th></tr>', esc_html( $label ), esc_attr( $id ), $container_attrs );
					} else {
						$content .= sprintf( '<tr%3$s><th scope="row">%1$s</th><td>%2$s</td></tr>', $field_label, $field_content, $container_attrs );
					}
				}
			}
		}
		/**
		 * Filters the settings fields content.
		 *
		 * @since 1.4
		 *
		 * @param string $content Settings fields content
		 * @param array $settings_fields Settings fields
		 * @param string $container Container for settings fields
		 */
		$content = apply_filters( 'awsm_jobs_settings_fields_content', $content, $settings_fields, $container );
		if ( $echo === true ) {
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $content;
		}
	}

	public function spec_template( $index, $tax_details = array(), $filters = array() ) {
		if ( ! empty( $tax_details ) && ! is_numeric( $index ) ) {
			return;
		}
		$spec_title = $row_data = $del_btn_data = $icon_option = $tag_options = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

		$spec_key_html = sprintf( '<input type="text" class="widefat awsm-jobs-spec-key" name="awsm_jobs_filter[%1$s][taxonomy]" value="" maxlength="32" placeholder="%2$s" title="%3$s" required /><input type="hidden" name="awsm_jobs_filter[%1$s][register]" value="true" />', esc_attr( $index ), esc_attr__( 'Specification key', 'wp-job-openings' ), esc_attr__( 'The job specification key should only contain alphanumeric, latin characters separated by hyphen/underscore, and cannot begin or end with a hyphen/underscore.', 'wp-job-openings' ) );

		if ( ! empty( $tax_details ) && isset( $tax_details['key'] ) && isset( $tax_details['options'] ) ) {
			$spec_key      = $tax_details['key'];
			$spec_options  = $tax_details['options'];
			$row_data      = sprintf( ' data-index="%s"', esc_attr( $index ) );
			$del_btn_data  = sprintf( ' data-taxonomy="%s"', esc_attr( $spec_key ) );
			$spec_title    = $spec_options->label;
			$spec_key_html = sprintf( '<input type="text" class="widefat" value="%2$s" disabled /><input type="hidden" name="awsm_jobs_filter[%1$s][taxonomy]" value="%2$s" />', esc_attr( $index ), esc_attr( $spec_key ) );
			foreach ( $filters as $filter ) {
				if ( $spec_key === $filter['taxonomy'] ) {
					if ( ! empty( $filter['icon'] ) ) {
						$icon_option = sprintf( '<option value="%1$s" selected><i class="awsm-job-icon-%1$s"></i> %1$s</option>', sanitize_html_class( $filter['icon'] ) );
					}
				}
			}
			$terms = get_terms(
				array(
					'taxonomy'   => $spec_key,
					'orderby'    => 'name',
					'hide_empty' => false,
				)
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$tag_options .= sprintf( '<option value="%1$s" data-termid="%2$s" selected>%1$s (%3$s)</option>', esc_attr( $term->name ), esc_attr( $term->term_id ), esc_attr( $term->count ) );
				}
			}
		}
		?>
			<tr class="awsm-job-specifications-settings-row"<?php echo $row_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<td class="awsm-specs-drag-control-wrap">
					<span class="awsm-specs-drag-control dashicons dashicons-move"></span>
				</td>
				<td>
					<input type="text" class="widefat awsm-jobs-spec-title" name="awsm_jobs_filter[<?php echo esc_attr( $index ); ?>][filter]" value="<?php echo esc_attr( $spec_title ); ?>" placeholder="<?php esc_html_e( 'Enter a specification', 'wp-job-openings' ); ?>" required />
				</td>
				<td>
					<?php echo $spec_key_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
				<td>
					<select class="awsm-font-icon-selector awsm-icon-select-control" name="awsm_jobs_filter[<?php echo esc_attr( $index ); ?>][icon]" style="width: 100%;" data-placeholder="<?php esc_html_e( 'Select icon', 'wp-job-openings' ); ?>"><?php echo $icon_option; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
				</td>
				<td>
					<select class="awsm_jobs_filter_tags" name="awsm_jobs_filter[<?php echo esc_attr( $index ); ?>][tags][]" multiple="multiple" style="width: 100%;" data-placeholder="<?php esc_html_e( 'Enter options', 'wp-job-openings' ); ?>"><?php echo $tag_options; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
				</td>
				<td><a class="button awsm-text-red awsm-filters-remove-row" href="#"<?php echo $del_btn_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php esc_html_e( 'Delete', 'wp-job-openings' ); ?></a>
				</td>
			</tr>
		<?php
	}

	public function get_template_tags() {
		$template_tags = apply_filters(
			'awsm_job_template_tags',
			array(
				'{applicant}'          => __( 'Applicant Name:', 'wp-job-openings' ),
				'{application-id}'     => __( 'Application ID:', 'wp-job-openings' ),
				'{applicant-email}'    => __( 'Applicant Email:', 'wp-job-openings' ),
				'{applicant-phone}'    => __( 'Applicant Phone:', 'wp-job-openings' ),
				'{applicant-resume}'   => __( 'Applicant Resume:', 'wp-job-openings' ),
				'{applicant-cover}'    => __( 'Cover letter:', 'wp-job-openings' ),
				'{job-title}'          => __( 'Job Title:', 'wp-job-openings' ),
				'{job-id}'             => __( 'Job ID:', 'wp-job-openings' ),
				'{job-expiry}'         => __( 'Job Expiry Date:', 'wp-job-openings' ),
				'{site-title}'         => __( 'Site Title:', 'wp-job-openings' ),
				'{site-tagline}'       => __( 'Site Tagline:', 'wp-job-openings' ),
				'{site-url}'           => __( 'Site URL:', 'wp-job-openings' ),
				'{admin-email}'        => __( 'Site admin email:', 'wp-job-openings' ),
				'{hr-email}'           => __( 'HR Email:', 'wp-job-openings' ),
				'{company}'            => __( 'Company Name:', 'wp-job-openings' ),
				'{author-email}'       => __( 'Author Email:', 'wp-job-openings' ),
				'{default-from-email}' => __( 'Default from email:', 'wp-job-openings' ),

			)
		);
		if ( get_option( 'awsm_hide_uploaded_files' ) === 'hide_files' ) {
			unset( $template_tags['{applicant-resume}'] );
		}
		return $template_tags;
	}

	public static function awsm_from_email( $set_as_empty = false ) {

		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
		$sitename = strtolower( $sitename );

		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}
		$from_email = 'noreply@' . $sitename;
		if ( ! $set_as_empty ) {
			$get_default_from_email = get_option( 'awsm_jobs_notification_customizer' );

			$from_email = isset( $get_default_from_email['from_email'] ) ? $get_default_from_email['from_email'] : '';

			if ( $from_email ) {
				return $from_email;
			}
		}

		return $from_email;
	}

	// New captcha funstions
	public static function get_captcha_config() {
		$config = array(
			'recaptcha' => array(
				'label'         => 'reCAPTCHA',
				'logo'          => 'data:image/png;base64,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',
				'verify_url'    => 'https://www.google.com/recaptcha/api/siteverify',
				'requires_ip'   => true,
				'signup_url'    => 'https://www.google.com/recaptcha/admin/create',
				'fail_message'  => 'reCAPTCHA verification failed. Please try again.',
				'requires_keys' => true,
			),
			'hcaptcha'  => array(
				'label'         => 'hCaptcha',
				'logo'          => 'data:image/png;base64,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',
				'verify_url'    => 'https://hcaptcha.com/siteverify',
				'requires_ip'   => true,
				'signup_url'    => 'https://dashboard.hcaptcha.com/signup',
				'fail_message'  => 'hCaptcha verification failed. Please try again.',
				'requires_keys' => true,
			),
			'turnstile' => array(
				'label'         => 'Turnstile',
				'logo'          => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFcAAABSCAYAAAAhBUjfAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAPnSURBVHgB7dlRchtFEADQ7l4LyvCBuABMThBzAtYBvrGL/GNOIP/yESxjKD5zBDnf4FL4p/DmBDgn8MIJlCpiGXu3m55VnEqcKJldrVbjSr8q2VvSuCS3Znp7egCMMcYYY4wxxhhjjDHGGGOMMcYYY4wx4RAiJ0PXf0qwRcC3ATEVAacful+9BjBBhFxQToTp0YcMD3GYTyAS0QZ3OnROiAcCuHMVzBBCckgJ7a9/n+ewYlEGd3rwyR4L7tYJ6nWIMly/988+rFBUwa1mayJjXe8b0I4ce7i5qlkcTXBnaUCO9dJBu1YW4CiCu8TAXllJgAkioKlgBMsLrOekqN6jUysP7vmBG2iOTWHZ9D3OfnS70KGVpoUO0sF1k3XGW13VwmuwZNMDp4U/f41EfWGeMNDjpAeZz38lQUrdBdbrnyH72TuEDixt5s6COj+X+jpUBHeg2+B6kw9++Ptj6MBSgnvxk/u2YDmESCFq5XAvz2DJWr+h+Twac2A9aW+T8kbt59wERr6jErM1hqz47Yst37uonhD0N7jHQJT1tv/IoCWtpoVnd/9TiFuuOfdWcXRnJCA711/UgOQMuP/eN38ewoJaTQucdLPcFoEaOP9b25Rbr3tdF522NGVUHG2eyjh1sIBWg6u5rHEXqzNaBl7+mqbwls/qg1wwnp4f3Wm88Yhi+9sVJDys+gtEaejfJCD3mwZ44RvayycFWrPGezPLNVKN+rs+wJfjL0/q3uwaB9cH9TzhwZnILlVNbazyQqRe6oqh8IlgvXs5cjnSHPwZbmfBW+dGaeHfn93GlOQv3WENFzkt6ARidr3duHY3e6hz4QRqmOVgqpUeas9cH1gsq2ZLdEGtDiz14a81v2b6xIN5O7ECZTthnRwEt3Wcg6D/R3xdPIRAtdZGG10sXY7b9CwA2riZJARBy6yLRrcuex/g/qU+sKQdzR+DVwZRshmae2vNXCbe0zrRwQK0hhz4zKz7+zzxF+X8sb6LJkJP/PXTfTd/HFZf0PMvKdHj9pDewcX4qw3g0tfmfZ/eCoaPdCb3CebXwcCc6s8MAgTP3Buy+3rR3KOdi3E6QE0J0CC1scDv79893goZGzxzS4TdG1YUXx3tbF494Zd9ITgGbn7yoTH4tMbYMEjyOdw0erQz/cW56tIHtsTjRY+UtIILnu3hk1E6b2q3IvlvFoyS6b4mwYV7H5rf89Cx4TM39np2jp7m3qk2YF7XAWuCGZ6Ejg0OrkBYyRQVhMwfRvaY9qAlWuVkoWPDSzHE7/gmdL1eRLNdmJZ0D4ToEbSgR5yBMcYYY4wxxhhjjDHGGGOMMcYYY4x5d/0P8gSEIITUIeUAAAAASUVORK5CYII=',
				'verify_url'    => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
				'requires_ip'   => false,
				'signup_url'    => 'https://dash.cloudflare.com/sign-up/turnstile',
				'fail_message'  => 'Turnstile verification failed. Please try again.',
				'requires_keys' => true,
			),
			'none'      => array(
				'label'         => 'None',
				'logo'          => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFcAAABSCAYAAAAhBUjfAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAM+SURBVHgB7ZyNcdpAEIVfOkgJ14HpICrBHaAO4lRgpQLTAXRgd6CkgjgViA5wB7beSDeWj5MAcb9iv5kdxmOEpM97ewvcGRAEQRAEQRAEQRAEQRAEQRBy4BvSRLVRtHHXP34fhGbfx2sb//vHVwhWKO6xjaaN95nBY5/Q/XEEdJlZY77QsdjihiUzUyng3XNEkxyr5q7b2OBrDTV5a+MFXT3d9z/v+9+p/lg+/mhjhWmBPO53GzssHNbEsSw7oJNe4HIoeIfpmv2EhcJMqzEutcJ0Jl9CiXHJ/xyeJwl4M7wp282eKg9zUegyefGCbWKZrSX8U/bnsgnOHluN5ZBdIQyPWGgNLmEXqxCGKbE6HpAhCseTygFpiQ19Tc7Y4vhGSoRhTGwF+2h6RkYo2N8phWBKrGZj+X2BTDCztkGYoXeOWMI2zOwgamSAQpxJ41yxmsry3FAdzGzMIces9d2wXyoWsGdvhcShzJC1do5YTYXjREgWhbBD7RqxpLAcq5AoJcJlwrViNWZpSPZNhVlvX+AHV2LJDmHL2Gxq+M8Cl2LJg/E6yX6gwzIwvNB7uMW1WHKPTCY186YLuMOHWFIYr3dAoviaeX2JJcryukniQ65PsUQhE7lmW1PgOnyLJQUykdvA3YQWQizJZkIzvyub24qFEkvMVqxGouxwfUMeUix5Ns6zQaKYWXBpWxNaLGmMc5VIFIX5vW4MsSv4ax+9YGZCdcYxMcSSLTKZzDQVjkvD1IflscTymsxESLbeamxDrRp5biyx5CcyKwmaGqezN6ZYhfDfmDijwLG04dKhmGLJFplmrcbsHxlrxBdbWs6dTdZqFOyrC2OKVZZrapDp3gnzTUVssY3l/CUyxrZ0KLRYdjA2scm3XucQc/HzGgte/Eymlu1v4afm8Zxjm1tual8Eh2wJN+idmGOTaY2FiR0yVYMpmZk8Z5VOgS5TpzqUoDU21ia/El12qYnn7NFtlP6L6U1+d+i+TTi1YfAXbmCTn0ZhfCuTy+BIWGwZOIWCH8k1Mlot7huFriY2mC+Ux1ZIJFNT/WcWq0Gwpip8rc9vg2BNZm3+g8+aLAiCIAiCIAiCIAiCIAiCIAhn8wGf0+Wm0s2UeAAAAABJRU5ErkJggg==',
				'verify_url'    => null,
				'requires_ip'   => false,
				'signup_url'    => null,
				'fail_message'  => 'CAPTCHA verification failed. Please try again.',
				'requires_keys' => false,
			),
		);

		return apply_filters( 'awsm_jobs_captcha_config', $config );
	}

	private function get_captcha_settings_options() {
		$options = array();
		$config  = self::get_captcha_config();

		foreach ( $config as $provider => $provider_config ) {
			if ( $provider === 'none' ) {
				continue;
			}

			// Skip reCAPTCHA in the loop - we'll handle it separately below
			if ( $provider === 'recaptcha' ) {
				continue;
			}

			// Handle other providers (hCaptcha, Turnstile, etc.)
			$site_key_option = self::get_captcha_data( 'field_name', $provider, 'site_key' );
			$options[]       = array(
				'option_name' => $site_key_option,
				'callback'    => function( $input ) use ( $provider ) {
					return $this->validate_captcha_key( $input, $provider, 'site_key' );
				},
			);

			$secret_key_option = self::get_captcha_data( 'field_name', $provider, 'secret_key' );
			$options[]         = array(
				'option_name' => $secret_key_option,
				'callback'    => function( $input ) use ( $provider ) {
					return $this->validate_captcha_key( $input, $provider, 'secret_key' );
				},
			);

			$fail_message_option = "awsm_jobs_{$provider}_fail_message";
			$options[]           = array(
				'option_name' => $fail_message_option,
				'callback'    => function( $input ) use ( $provider ) {
					return $this->sanitize_captcha_fail_message( $input, $provider );
				},
			);
		}

		// Handle reCAPTCHA separately with both v2 and v3 keys
		// v2/v2_invisible keys
		$options[] = array(
			'option_name' => 'awsm_jobs_recaptcha_site_key',
			'callback'    => function( $input ) {
				return $this->validate_captcha_key( $input, 'recaptcha', 'site_key', 'awsm_jobs_recaptcha_site_key' );
			},
		);
		$options[] = array(
			'option_name' => 'awsm_jobs_recaptcha_secret_key',
			'callback'    => function( $input ) {
				return $this->validate_captcha_key( $input, 'recaptcha', 'secret_key', 'awsm_jobs_recaptcha_secret_key' );
			},
		);

		// reCAPTCHA v3
		$options[] = array(
			'option_name' => 'awsm_jobs_recaptcha_v3_site_key',
			'callback'    => function( $input ) {
				return $this->validate_captcha_key( $input, 'recaptcha', 'site_key', 'awsm_jobs_recaptcha_v3_site_key' );
			},
		);
		$options[] = array(
			'option_name' => 'awsm_jobs_recaptcha_v3_secret_key',
			'callback'    => function( $input ) {
				return $this->validate_captcha_key( $input, 'recaptcha', 'secret_key', 'awsm_jobs_recaptcha_v3_secret_key' );
			},
		);

		// reCAPTCHA fail message
		$options[] = array(
			'option_name' => 'awsm_jobs_recaptcha_fail_message',
			'callback'    => function( $input ) {
				return $this->sanitize_captcha_fail_message( $input, 'recaptcha' );
			},
		);

		// reCAPTCHA type selector
		$options[] = array(
			'option_name' => 'awsm_jobs_recaptcha_type',
			'callback'    => array( $this, 'sanitize_recaptcha_type' ),
		);

		// No-conflict mode
		$options[] = array(
			'option_name' => 'awsm_jobs_captcha_no_conflict_scripts',
			'callback'    => array( $this, 'sanitize_captcha_no_conflict_scripts' ),
		);

		return apply_filters( 'awsm_jobs_captcha_settings_options', $options, $config );
	}

	public static function get_captcha_data( $type = 'config', $provider = null, $key_type = null ) {
		$config = self::get_captcha_config();

		if ( $provider == null ) {
			$provider = self::get_current_captcha_provider();
		}

		$provider_config = isset( $config[ $provider ] ) ? $config[ $provider ] : $config['none'];

		switch ( $type ) {
			case 'config':
				return $provider_config;
			case 'field_name':
				if ( $key_type == null ) {
					return null;
				}
				if ( $provider === 'recaptcha' ) {
					$recaptcha_type = get_option( 'awsm_jobs_recaptcha_type', 'v2' );
					if ( $recaptcha_type === 'v3' ) {
						return "awsm_jobs_recaptcha_v3_{$key_type}";
					}
					return $key_type === 'site_key' ? 'awsm_jobs_recaptcha_site_key' : 'awsm_jobs_recaptcha_secret_key';
				}
				return "awsm_jobs_{$provider}_{$key_type}";
			case 'key_value':
				if ( $key_type == null ) {
					return null;
				}
				$field_name = self::get_captcha_data( 'field_name', $provider, $key_type );
				return get_option( $field_name, '' );
			case 'button_text':
				if ( empty( $provider_config['label'] ) ) {
					return '';
				}
				/* translators: %s: Captcha provider name (e.g. hCaptcha, reCAPTCHA) */
				return sprintf( __( 'Get %s keys', 'wp-job-openings' ), $provider_config['label'] );
			case 'fail_message':
				$field  = "awsm_jobs_{$provider}_fail_message";
				$custom = get_option( $field, '' );
				if ( $custom !== '' ) {
					return $custom;
				}

				if ( ! empty( $provider_config['label'] ) ) {
					/* translators: %s: Captcha provider name (e.g. hCaptcha, reCAPTCHA) */
					return sprintf( __( '%s verification failed. Please try again.', 'wp-job-openings' ), $provider_config['label'] );
				}

				return __( 'CAPTCHA verification failed. Please try again.', 'wp-job-openings' );
			default:
				return null;
		}
	}

	public static function get_captcha_settings_fields() {
		$config = self::get_captcha_config();
		$fields = array();

		$fields[] = array(
			'id'    => 'awsm-form-recaptcha-options-title',
			'label' => __( 'CAPTCHA Options', 'wp-job-openings' ),
			'type'  => 'title',
		);

		$fields[] = array(
			'name'          => 'awsm_jobs_enable_captcha',
			'label'         => __( 'Enable CAPTCHA', 'wp-job-openings' ),
			'type'          => 'radio',
			'choices'       => self::get_captcha_choices(),
			'default_value' => 'none',
			'class'         => 'awsm-captcha-provider-group',
		);

		foreach ( $config as $provider => $provider_config ) {
			if ( $provider === 'none' ) {
				continue;
			}

			if ( $provider === 'recaptcha' ) {
				$recaptcha_enable_opt = self::get_current_captcha_provider();
				$recaptcha_type       = get_option( 'awsm_jobs_recaptcha_type', 'v2' );

				$field_attributes = array();
				if ( $recaptcha_enable_opt !== 'recaptcha' ) {
					$field_attributes = array( 'disabled' => 'disabled' );
				}

				$fields[] = array(
					'name'          => 'awsm_jobs_recaptcha_type',
					'label'         => __( 'reCAPTCHA type', 'wp-job-openings' ),
					'type'          => 'radio',
					'choices'       => array(
						array(
							'value' => 'v2',
							'text'  => __( 'reCAPTCHA v2', 'wp-job-openings' ),
						),
						array(
							'value' => 'v2_invisible',
							'text'  => __( 'reCAPTCHA v2 Invisible', 'wp-job-openings' ),
						),
						array(
							'value' => 'v3',
							'text'  => __( 'reCAPTCHA v3', 'wp-job-openings' ),
						),
					),
					'default_value' => $recaptcha_type,
					'attributes'    => $field_attributes,
					'class'         => 'awsm-captcha-panel awsm-captcha-panel-recaptcha awsm-recaptcha-type-selector',
					'row_class'     => 'awsm-hide awsm-captcha-row awsm-captcha-row-recaptcha',
					'description'   => __(
						'<strong>IMPORTANT NOTE:</strong><br>reCAPTCHA v2 and v3 Site key and Secret key are different. Using invalid keys will cause a reCAPTCHA error leading to issues with the job application form. Please verify the keys before updating the settings.',
						'wp-job-openings'
					),
				);

				// v2/v2_invisible Site Key
				$fields[] = array(
					'name'        => 'awsm_jobs_recaptcha_site_key',
					'label'       => __( 'Site key', 'wp-job-openings' ),
					'class'       => 'regular-text awsm-captcha-panel awsm-captcha-panel-recaptcha',
					'row_class'   => 'awsm-hide awsm-captcha-row awsm-captcha-row-recaptcha awsm-recaptcha-key-v2',
					'help_button' => array(
						'url'         => $provider_config['signup_url'],
						'class'       => 'button button-secondary awsm-view-captcha-btn',
						'text'        => self::get_captcha_data( 'button_text', $provider ),
						'other_attrs' => array(
							'target' => '_blank',
							'rel'    => 'noopener',
						),
					),
				);

				// v2/v2_invisible Secret Key
				$fields[] = array(
					'name'      => 'awsm_jobs_recaptcha_secret_key',
					'label'     => __( 'Secret key', 'wp-job-openings' ),
					'class'     => 'regular-text awsm-captcha-panel awsm-captcha-panel-recaptcha',
					'row_class' => 'awsm-hide awsm-captcha-row awsm-captcha-row-recaptcha awsm-recaptcha-key-v2',

				);

				// v3 Site Key
				$fields[] = array(
					'name'        => 'awsm_jobs_recaptcha_v3_site_key',
					'label'       => __( 'Site key', 'wp-job-openings' ),
					'class'       => 'regular-text awsm-captcha-panel awsm-captcha-panel-recaptcha',
					'row_class'   => 'awsm-hide awsm-captcha-row awsm-captcha-row-recaptcha awsm-recaptcha-key-v3',
					'help_button' => array(
						'url'         => $provider_config['signup_url'],
						'class'       => 'button button-secondary awsm-view-captcha-btn',
						'text'        => self::get_captcha_data( 'button_text', $provider ),
						'other_attrs' => array(
							'target' => '_blank',
							'rel'    => 'noopener',
						),
					),
				);

				// v3 Secret Key
				$fields[] = array(
					'name'      => 'awsm_jobs_recaptcha_v3_secret_key',
					'label'     => __( 'Secret key ', 'wp-job-openings' ),
					'class'     => 'regular-text awsm-captcha-panel awsm-captcha-panel-recaptcha',
					'row_class' => 'awsm-hide awsm-captcha-row awsm-captcha-row-recaptcha awsm-recaptcha-key-v3',

				);
			} else {
				// Non-reCAPTCHA providers
				$site_key_field = array(
					'name'      => self::get_captcha_data( 'field_name', $provider, 'site_key' ),
					'label'     => __( 'Site key', 'wp-job-openings' ),
					'class'     => 'regular-text awsm-captcha-panel awsm-captcha-panel-' . $provider,
					'row_class' => 'awsm-hide awsm-captcha-row awsm-captcha-row-' . $provider,

				);

				if ( ! empty( $provider_config['signup_url'] ) ) {
					$site_key_field['help_button'] = array(
						'url'         => $provider_config['signup_url'],
						'class'       => 'button button-secondary awsm-view-captcha-btn',
						'text'        => self::get_captcha_data( 'button_text', $provider ),
						'other_attrs' => array(
							'target' => '_blank',
							'rel'    => 'noopener',
						),
					);
				}

				$fields[] = $site_key_field;

				$fields[] = array(
					'name'      => self::get_captcha_data( 'field_name', $provider, 'secret_key' ),
					'label'     => __( 'Secret key', 'wp-job-openings' ),
					'class'     => 'regular-text awsm-captcha-panel awsm-captcha-panel-' . $provider,
					'row_class' => 'awsm-hide awsm-captcha-row awsm-captcha-row-' . $provider,

				);
			}

			$fields[] = array(
				'name'          => "awsm_jobs_{$provider}_fail_message",
				'label'         => __( 'Fail Message', 'wp-job-openings' ),
				'class'         => 'regular-text awsm-captcha-panel awsm-captcha-panel-' . $provider,
				'row_class'     => 'awsm-hide awsm-captcha-row awsm-captcha-row-' . $provider,
				'description'   => __( 'Displays to users who fail the verification process.', 'wp-job-openings' ),
				/* translators: %s: Captcha provider name (e.g. hCaptcha, reCAPTCHA) */
				'default_value' => ! empty( $provider_config['label'] ) ? sprintf( __( '%s verification failed. Please try again.', 'wp-job-openings' ), $provider_config['label'] ) : __( 'CAPTCHA verification failed. Please try again.', 'wp-job-openings' ),
			);

		}

		$fields[] = array(
			'name'      => 'awsm_jobs_captcha_no_conflict_scripts',
			'label'     => __( 'No-Conflict Mode', 'wp-job-openings' ),
			'type'      => 'checkbox',
			'choices'   => array(
				array(
					'value' => 'on',
					'text'  => __( 'Prevent conflicts with other CAPTCHA plugins/scripts', 'wp-job-openings' ),
				),
			),
			'class'     => 'awsm-captcha-common',
			'row_class' => 'awsm-hide awsm-captcha-row awsm-captcha-row-common',
		);

		return $fields;
	}

	public static function set_captcha_labels() {
		$config = self::get_captcha_config();
		$labels = array();

		foreach ( $config as $key => $data ) {
			$labels[ $key ] = $data['label'];
		}

		return $labels;
	}

	public static function get_captcha_choices() {
		$labels = self::set_captcha_labels();

		$choices = array();
		foreach ( $labels as $value => $text ) {
			$choices[] = array(
				'value' => $value,
				'text'  => $text,
			);
		}

		return $choices;
	}

	/**
	 * Get current selected CAPTCHA provider
	 *
	 * @return string
	 */
	public static function get_current_captcha_provider() {
		return get_option( 'awsm_jobs_enable_captcha', 'none' );
	}

	private function get_provider_config( $provider ) {
		$config = self::get_captcha_config();
		return isset( $config[ $provider ] ) ? $config[ $provider ] : null;
	}

	/**
	 * Render only the CAPTCHA settings fields with proper <tr> row_class support.
	 *
	 * @param array $fields The settings array, e.g. $settings_fields['recaptcha'].
	 */
	public function display_captcha_settings_fields( array $fields ) {
		foreach ( $fields as $field ) {
			if ( isset( $field['type'] ) && $field['type'] === 'title' ) {
				$label = isset( $field['label'] ) ? esc_html( $field['label'] ) : '';
				echo '<tr class="awsm-settings-row awsm-captcha-title-row">';
				echo '<th scope="row" colspan="2"><h2>' . esc_html( $label ) . '</h2></th>';
				echo '</tr>';
				continue;
			}

			$name        = isset( $field['name'] ) ? esc_attr( $field['name'] ) : '';
			$label       = isset( $field['label'] ) ? esc_html( $field['label'] ) : '';
			$type        = isset( $field['type'] ) ? $field['type'] : 'text';
			$class       = isset( $field['class'] ) ? esc_attr( $field['class'] ) : '';
			$row_class   = isset( $field['row_class'] ) ? esc_attr( $field['row_class'] ) : '';
			$description = isset( $field['description'] ) ? wp_kses_post( $field['description'] ) : '';
			$default     = isset( $field['default_value'] ) ? $field['default_value'] : '';
			$help_button = isset( $field['help_button'] ) ? $field['help_button'] : false;
			$required    = isset( $field['required'] ) && $field['required'] === true;

			$value       = get_option( $name, $default );
			$row_classes = trim( 'awsm-settings-row ' . $row_class );

			echo '<tr class="' . esc_attr( $row_classes ) . '">';
			echo '<th scope="row">';
			if ( $label ) {
				echo '<label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label>';
			}
			echo '</th>';
			echo '<td>';

			switch ( $type ) {
				case 'radio':
					$choices             = isset( $field['choices'] ) ? (array) $field['choices'] : array();
					$captcha_config      = self::get_captcha_config();
					$is_captcha_provider = ( $name === 'awsm_jobs_enable_captcha' );

					if ( $is_captcha_provider ) {
						if ( $value === 'none' || empty( $value ) ) {
							$enable_recaptcha = get_option( 'awsm_jobs_enable_recaptcha' );
							$site_key         = get_option( 'awsm_jobs_recaptcha_site_key' );
							$secret_key       = get_option( 'awsm_jobs_recaptcha_secret_key' );
							$recaptcha_type   = get_option( 'awsm_jobs_recaptcha_type' );

							// Migration: only when type is v3, shared fields have keys, AND v3-specific fields are empty.
							// If v3 fields already have values, shared fields belong to v2 — don't touch them.
							if (
								$recaptcha_type === 'v3'
								&& ! empty( $site_key )
								&& ! empty( $secret_key )
								&& empty( get_option( 'awsm_jobs_recaptcha_v3_site_key' ) )
								&& empty( get_option( 'awsm_jobs_recaptcha_v3_secret_key' ) )
							) {
								// Move to v3 fields — these are old version leftovers
								update_option( 'awsm_jobs_recaptcha_v3_site_key', $site_key );
								update_option( 'awsm_jobs_recaptcha_v3_secret_key', $secret_key );

								// Now safe to clear — confirmed these were v3 keys from old version
								update_option( 'awsm_jobs_recaptcha_site_key', '' );
								update_option( 'awsm_jobs_recaptcha_secret_key', '' );

								// Set value based on enable status
								if ( $enable_recaptcha === 'enable' ) {
									$value = 'recaptcha';
									update_option( $name, 'recaptcha' );
								} else {
									$value = 'none';
									update_option( $name, 'none' );
								}
							} elseif ( $enable_recaptcha === 'enable' && ! empty( $site_key ) && ! empty( $secret_key ) ) {
								// v2 / v2 invisible — shared fields are the correct home, use as-is
								$value = 'recaptcha';
								update_option( $name, 'recaptcha' );
							} else {
								$value = 'none';
								update_option( $name, 'none' );
							}
						} elseif ( $value === 'recaptcha' ) {
							$enable_recaptcha = get_option( 'awsm_jobs_enable_recaptcha' );

							if ( $enable_recaptcha !== 'enable' ) {
								$value = 'none';
								update_option( $name, 'none' );
							}
						}
						echo '<div class="awsm-captcha-wrapper">';
						foreach ( $choices as $choice ) {
							$val  = isset( $choice['value'] ) ? esc_attr( $choice['value'] ) : '';
							$text = isset( $choice['text'] ) ? esc_html( $choice['text'] ) : $val;

							$logo = '';
							if ( isset( $captcha_config[ $val ]['logo'] ) && $captcha_config[ $val ]['logo'] ) {
								$logo_src = esc_attr( $captcha_config[ $val ]['logo'] );
								$logo     = '<img src="' . $logo_src . '" alt="' . esc_attr( $text ) . '">';
							}

							$checked = checked( $value, $val, false );

							echo '<div class="awsm-captcha-item">';
							echo '<label>';
							echo '<input type="radio" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" ' . esc_attr( $checked ) . ' class="' . esc_attr( $class ) . '">';
							echo '<span>';
							if ( $logo ) {
								echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							echo esc_html( $text );
							echo '</span>';
							echo '</label>';
							echo '</div>';
						}
						echo '</div>';
					} else {
						$is_captcha_type = ( $name === 'awsm_jobs_recaptcha_type' );

						if ( $is_captcha_type ) {
							echo '<div class="awsm-recaptcha-type">';
						}

						foreach ( $choices as $choice ) {
							$val  = isset( $choice['value'] ) ? esc_attr( $choice['value'] ) : '';
							$text = isset( $choice['text'] ) ? esc_html( $choice['text'] ) : $val;

							echo '<label>';
							printf( '<input type="radio" name="%1$s" id="%1$s-%2$s" value="%2$s" %3$s class="%4$s" /> %5$s', esc_attr( $name ), esc_attr( $val ), checked( $value, $val, false ), esc_attr( $class ), esc_html( $text ) );
							echo '</label>';
						}

						if ( $is_captcha_type ) {
							echo '</div>';
						}
					}
					break;
				case 'checkbox':
					$choices   = isset( $field['choices'] ) ? (array) $field['choices'] : array();
					$is_toggle = ! empty( $field['toggle'] ) || count( $choices ) === 1;

					if ( $is_toggle ) {
						$choice  = reset( $choices );
						$val     = isset( $choice['value'] ) ? esc_attr( $choice['value'] ) : 'on';
						$text    = isset( $choice['text'] ) ? esc_html( $choice['text'] ) : '';
						$checked = checked( $value, $val, false );

						printf( '<span class="awsm-setting-field"><span class="awsm-toggle-control"><input type="checkbox" role="switch" aria-checked="%8$s" name="%1$s" id="%1$s" value="%2$s" %3$s class="%4$s" /><label class="awsm-toggle-control-icon" for="%1$s"><span class="awsm-captcha-toggle-slider" aria-hidden="true"></span></label><label for="%1$s" class="awsm-toggle-control-status" data-on="%6$s" data-off="%7$s">%7$s</label></span><p class="awsm-captcha-toggle-label">%5$s</p></span>', esc_attr( $name ), esc_attr( $val ), esc_attr( $checked ), esc_attr( $class ), esc_html( $text ), esc_html__( 'On', 'wp-job-openings' ), esc_html__( 'Off', 'wp-job-openings' ), $checked ? 'true' : 'false' );

					} else {
						foreach ( $choices as $choice ) {
							$val  = isset( $choice['value'] ) ? esc_attr( $choice['value'] ) : 'on';
							$text = isset( $choice['text'] ) ? esc_html( $choice['text'] ) : '';
							printf( '<label><input type="checkbox" name="%1$s[]" value="%2$s" %3$s class="%4$s" /> %5$s</label>', esc_attr( $name ), esc_attr( $val ), checked( is_array( (array) $value ) && in_array( esc_attr( $val ), (array) $value, true ) || $value === $val, true, false ), esc_attr( $class ), esc_html( $text ) );
						}
					}
					break;

				case 'text':
				default:
					$is_captcha_key = ( strpos( $name, '_site_key' ) !== false || strpos( $name, '_secret_key' ) !== false );

					if ( $is_captcha_key && $help_button ) {
						echo '<div class="awsm-captcha-key-gen">';
					}

					$required_attr = $required ? ' required' : '';
					printf(
						'<input type="text" class="%1$s" id="%2$s" name="%2$s" value="%3$s"%4$s />',
						esc_attr( $class ? $class : 'regular-text' ),
						esc_attr( $name ),
						esc_attr( $value ),
						esc_attr( $required_attr )
					);

					if ( $is_captcha_key && $help_button && isset( $help_button['url'], $help_button['text'] ) ) {
						$hb_url   = esc_url( $help_button['url'] );
						$hb_class = isset( $help_button['class'] ) ? esc_attr( $help_button['class'] ) : 'button button-secondary';
						$hb_text  = esc_html( $help_button['text'] );
						$other    = '';
						if ( isset( $help_button['other_attrs'] ) && is_array( $help_button['other_attrs'] ) ) {
							foreach ( $help_button['other_attrs'] as $k => $v ) {
								$other .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
							}
						}
						echo '<a href="' . esc_url( $hb_url ) . '" class="' . esc_attr( $hb_class ) . '" ' . $other . '>' . esc_html( $hb_text ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</div>';

						$help_button = false;
					}

					break;
			}

			if ( $help_button && isset( $help_button['url'], $help_button['text'] ) ) {
				$awsm_help_button_url   = esc_url( $help_button['url'] );
				$awsm_help_button_class = isset( $help_button['class'] ) ? esc_attr( $help_button['class'] ) : 'button button-secondary';
				$awsm_help_button_text  = esc_html( $help_button['text'] );

				$other = '';
				if ( isset( $help_button['other_attrs'] ) && is_array( $help_button['other_attrs'] ) ) {
					foreach ( $help_button['other_attrs'] as $k => $v ) {
						$other .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
					}
				}
				echo '<a href="' . esc_url( $awsm_help_button_url ) . '" class="' . esc_attr( $awsm_help_button_class ) . '"' . esc_attr( $other ) . '>' . esc_html( $awsm_help_button_text ) . '</a>';
			}

			if ( $description ) {
				echo '<p class="description">' . wp_kses_post( $description ) . '</p>';
			}

			echo '</td>';
			echo '</tr>';
		}
	}

	public function sanitize_captcha_fail_message( $input, $provider ) {
		$option_name      = "awsm_jobs_{$provider}_fail_message";
		$current_provider = self::get_current_captcha_provider();

		if ( empty( $input ) && $provider === $current_provider ) {
			$default_message = self::get_captcha_data( 'fail_message', $provider );

			add_settings_error(
				$option_name,
				'awsm-captcha-fail-message-empty',
				sprintf(
					/* translators: %s: provider label */
					esc_html__( 'The %s fail message cannot be empty. Default message has been restored.', 'wp-job-openings' ),
					esc_html( self::get_captcha_data( 'config', $provider )['label'] )
				),
				'warning'
			);

			return $default_message;
		}

		if ( empty( $input ) ) {
			return '';
		}

		$value = wp_kses_post( trim( $input ) );

		if ( $provider === $current_provider && strlen( $value ) < 5 ) {
			$old_value = get_option( $option_name, '' );

			add_settings_error(
				$option_name,
				'awsm-captcha-fail-message-short',
				sprintf(
					/* translators: %s: provider label */
					esc_html__( 'The %s fail message is too short. Please provide at least 5 characters.', 'wp-job-openings' ),
					esc_html( self::get_captcha_data( 'config', $provider )['label'] )
				),
				'error'
			);

			return ! empty( $old_value ) ? $old_value : self::get_captcha_data( 'fail_message', $provider );
		}

		return $value;
	}

	/**
	 * Get fail message for a provider
	 */
	public function get_captcha_fail_message( $provider = null ) {
		if ( $provider === null ) {
			$provider = $this->get_current_captcha_provider();
		}

		$option_name    = "awsm_jobs_{$provider}_fail_message";
		$custom_message = get_option( $option_name, '' );

		if ( ! empty( $custom_message ) ) {
			return $custom_message;
		}

		$provider_config = $this->get_provider_config( $provider );
		if ( $provider_config && ! empty( $provider_config['fail_message'] ) ) {
			return $provider_config['fail_message'];
		}

		return __( 'CAPTCHA verification failed. Please try again.', 'wp-job-openings' );
	}

	public function sanitize_captcha_no_conflict_scripts( $input ) {
		$old_value = get_option( 'awsm_jobs_captcha_no_conflict_scripts', '' );
		$new_value = ! empty( $input ) && 'on' == $input ? 'on' : '';

		if ( $old_value !== $new_value ) {
			$status_text = ( 'on' == $new_value )
				? __( 'No-Conflict Mode enabled.', 'wp-job-openings' )
				: __( 'No-Conflict Mode disabled.', 'wp-job-openings' );

			add_settings_error(
				'awsm_jobs_captcha_no_conflict_scripts',
				'awsm-captcha-no-conflict-updated',
				$status_text,
				'success'
			);
		}

		return $new_value;
	}

	public function is_no_conflict_mode_enabled() {
		return 'on' === get_option( 'awsm_jobs_captcha_no_conflict_scripts', '' );
	}

	public function sanitize_recaptcha_type( $input ) {
		$allowed_types = array( 'v2', 'v2_invisible', 'v3' );
		$sanitized     = sanitize_text_field( $input );
		return in_array( $sanitized, $allowed_types, true ) ? $sanitized : 'v2';
	}

	/**
	 * Verify CAPTCHA keys by making a test API call.
	 *
	 * @param string $site_key
	 * @param string $secret_key
	 * @param string $provider
	 * @return array { 'valid' => bool, 'message' => string }
	 */
	private function verify_keys_with_api( $site_key, $secret_key, $provider ) {
		$provider_config = $this->get_provider_config( $provider );

		if ( ! $provider_config || empty( $provider_config['verify_url'] ) ) {
			return array(
				'valid'   => false,
				'message' => __( 'Provider configuration error.', 'wp-job-openings' ),
			);
		}

		$verify_url = $provider_config['verify_url'];

		$body = array(
			'secret'   => $secret_key,
			'response' => 'test_validation_' . wp_generate_password( 20, false ),
		);

		if ( 'hcaptcha' === $provider ) {
			$body['sitekey'] = $site_key;
		}

		$args = array(
			'body'    => $body,
			'timeout' => 10,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
		);

		/**
		 * Filter HTTP request args for CAPTCHA verification.
		 *
		 * Keep this minimal—useful for adjusting timeouts, headers, proxies, etc.
		 *
		 * @param array  $args
		 * @param string $verify_url
		 * @param string $provider
		 * @param array  $provider_config
		 */
		$args = apply_filters( 'awsm_jobs_captcha_verify_request_args', $args, $verify_url, $provider, $provider_config );

		/**
		 * Action: fired just before making the verification HTTP request.
		 *
		 * @param string $verify_url
		 * @param array  $args
		 * @param string $provider
		 * @param array  $provider_config
		 */
		do_action( 'awsm_jobs_captcha_verify_request', $verify_url, $args, $provider, $provider_config );

		$response = wp_remote_post( $verify_url, $args );

		if ( is_wp_error( $response ) ) {
			/**
			 * Action: fired when the HTTP request fails.
			 *
			 * @param WP_Error $response
			 * @param string   $provider
			 * @param array    $provider_config
			 */
			do_action( 'awsm_jobs_captcha_verify_http_error', $response, $provider, $provider_config );
			return array(
				'valid'   => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Connection error: %s', 'wp-job-openings' ),
					$response->get_error_message()
				),
			);
		}

		$raw_body = wp_remote_retrieve_body( $response );

		$data = json_decode( $raw_body, true );

		if ( ! is_array( $data ) ) {
			return array(
				'valid'   => false,
				'message' => __( 'Invalid API response.', 'wp-job-openings' ),
			);
		}

		$result = $this->analyze_api_response( $data, $provider_config );

		/**
		 * Filter the final verification result.
		 *
		 * Use this to change the message or force valid/invalid.
		 *
		 * @param array  $result
		 * @param array  $data
		 * @param string $provider
		 * @param array  $provider_config
		 */
		$result = apply_filters( 'awsm_jobs_captcha_verify_result', $result, $data, $provider, $provider_config );
		/**
		 * Action: fired after verification finishes (success or failure).
		 *
		 * @param array  $result
		 * @param array  $data
		 * @param string $provider
		 * @param array  $provider_config
		 */
		do_action( 'awsm_jobs_captcha_verify_completed', $result, $data, $provider, $provider_config );

		return $result;
	}

	/**
	 * Analyze API response to determine key validity.
	 *
	 * @param array $data
	 * @param array $provider_config
	 * @return array Array with 'valid' boolean and 'message' string.
	 */
	private function analyze_api_response( $data, $provider_config ) {
		$provider_name = ! empty( $provider_config['label'] )
			? $provider_config['label']
			: __( 'CAPTCHA', 'wp-job-openings' );

		$error_patterns = array(
			/* translators: %s: CAPTCHA service name (e.g., reCAPTCHA, hCaptcha, Turnstile) */
			'invalid-input-secret'    => __( 'Invalid Secret Key. Please verify your %s Secret Key.', 'wp-job-openings' ),
			'missing-input-secret'    => __( 'Secret Key is missing.', 'wp-job-openings' ),
			/* translators: %s: CAPTCHA service name */
			'sitekey-secret-mismatch' => __( 'Site Key and Secret Key do not match. Please verify both keys belong to the same account.', 'wp-job-openings' ),
		);

		/**
		 * Filter error patterns map.
		 *
		 * @param array $error_patterns
		 * @param array $provider_config
		 * @param array $data
		 */
		$error_patterns = apply_filters( 'awsm_jobs_captcha_error_patterns', $error_patterns, $provider_config, $data );

		if ( isset( $data['error-codes'] ) && is_array( $data['error-codes'] ) ) {
			$error_codes = $data['error-codes'];

			foreach ( $error_patterns as $error_code => $message_template ) {
				if ( in_array( $error_code, $error_codes, true ) ) {
					return array(
						'valid'   => false,
						'message' => sprintf( $message_template, $provider_name ),
					);
				}
			}

			$expected_test_errors = array(
				'missing-input-response',
				'invalid-input-response',
				'timeout-or-duplicate',
			);

			foreach ( $expected_test_errors as $test_error ) {
				if ( in_array( $test_error, $error_codes, true ) ) {
					return array(
						'valid'   => true,
						'message' => __( 'Keys verified successfully.', 'wp-job-openings' ),
					);
				}
			}

			return array(
				'valid'   => false,
				'message' => sprintf(
					/* translators: %s: error codes */
					__( 'API returned error: %s', 'wp-job-openings' ),
					implode( ', ', $error_codes )
				),
			);
		}

		if ( isset( $data['success'] ) && true === $data['success'] ) {
			return array(
				'valid'   => true,
				'message' => __( 'Keys verified successfully.', 'wp-job-openings' ),
			);
		}

		return array(
			'valid'   => false,
			'message' => __( 'Unable to verify keys. Please check your configuration.', 'wp-job-openings' ),
		);
	}


	/**
	 * Validates and sanitizes CAPTCHA API keys.
	 *
	 * @param mixed  $input               The input value to validate.
	 * @param string $provider            The CAPTCHA provider (e.g., 'recaptcha', 'hcaptcha').
	 * @param string $key_type            Type of key ('site_key' or 'secret_key').
	 * @param string $actual_option_name  Optional. The actual option name being validated.
	 * @return string The validated and sanitized value.
	 */
	private function validate_captcha_key( $input, $provider, $key_type, $actual_option_name = null ) {
		$option_name      = ( null !== $actual_option_name ) ? $actual_option_name : self::get_captcha_data( 'field_name', $provider, $key_type );
		$old_value        = get_option( $option_name, '' );
		$current_provider = $this->get_current_captcha_provider();

		if ( ! $this->is_active_recaptcha_option( $provider, $key_type, $actual_option_name ) ) {
			return $this->sanitize_and_filter( $input, $provider, $key_type, $option_name );
		}

		if ( $provider !== $current_provider ) {
			return $this->sanitize_and_filter( $input, $provider, $key_type, $option_name );
		}

		if ( ! $this->provider_requires_keys( $provider ) ) {
			return $this->sanitize_and_filter( $input, $provider, $key_type, $option_name );
		}

		if ( empty( $input ) || ! is_string( $input ) ) {
			$this->add_empty_key_error( $provider, $key_type, $option_name );
			return $old_value;
		}

		$value = sanitize_text_field( trim( $input ) );

		$bypass = apply_filters( 'awsm_jobs_captcha_validate_short_circuit', null, $value, $provider, $key_type, $option_name );
		if ( null !== $bypass ) {
			return $this->sanitize_and_filter( (string) $bypass, $provider, $key_type, $option_name );
		}

		if ( $value === $old_value && '' !== $old_value ) {
			return $this->sanitize_and_filter( $value, $provider, $key_type, $option_name );
		}

		if ( 'secret_key' === $key_type ) {
			$this->verify_secret_key( $value, $old_value, $provider, $key_type, $option_name, $actual_option_name );
		}

		return $this->sanitize_and_filter( $value, $provider, $key_type, $option_name );
	}

	/**
	 * Checks if the current option is the active reCAPTCHA version option.
	 *
	 * @param string      $provider            The CAPTCHA provider.
	 * @param string      $key_type            Type of key ('site_key' or 'secret_key').
	 * @param string|null $actual_option_name  The actual option name being validated.
	 * @return bool True if this is the active option or not a reCAPTCHA option.
	 */
	private function is_active_recaptcha_option( $provider, $key_type, $actual_option_name ) {
		if ( 'recaptcha' !== $provider || null === $actual_option_name ) {
			return true;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$submitted_type = isset( $_POST['awsm_jobs_recaptcha_type'] )
			? sanitize_text_field( $_POST['awsm_jobs_recaptcha_type'] )
			: get_option( 'awsm_jobs_recaptcha_type', 'v2' );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$active_option_name = $this->get_active_recaptcha_option_name( $submitted_type, $key_type );

		return $actual_option_name === $active_option_name;
	}

	/**
	 * Gets the active reCAPTCHA option name based on version and key type.
	 *
	 * @param string $version  The reCAPTCHA version ('v2' or 'v3').
	 * @param string $key_type Type of key ('site_key' or 'secret_key').
	 * @return string The option name.
	 */
	private function get_active_recaptcha_option_name( $version, $key_type ) {
		if ( 'v3' === $version ) {
			return "awsm_jobs_recaptcha_v3_{$key_type}";
		}

		return 'site_key' === $key_type
			? 'awsm_jobs_recaptcha_site_key'
			: 'awsm_jobs_recaptcha_secret_key';
	}

	/**
	 * Checks if the provider requires API keys.
	 *
	 * @param string $provider The CAPTCHA provider.
	 * @return bool True if provider requires keys.
	 */
	private function provider_requires_keys( $provider ) {
		$provider_config = $this->get_provider_config( $provider );
		return ! empty( $provider_config['requires_keys'] );
	}

	/**
	 * Adds a settings error for empty CAPTCHA keys.
	 *
	 * @param string $provider    The CAPTCHA provider.
	 * @param string $key_type    Type of key.
	 * @param string $option_name The option name.
	 */
	private function add_empty_key_error( $provider, $key_type, $option_name ) {
		$provider_config = $this->get_provider_config( $provider );
		$service_name    = ! empty( $provider_config['label'] ) ? $provider_config['label'] : __( 'CAPTCHA', 'wp-job-openings' );
		$key_label       = ( 'site_key' === $key_type )
			? __( 'Site Key', 'wp-job-openings' )
			: __( 'Secret Key', 'wp-job-openings' );

		$message = sprintf(
			/* translators: %s: Key label (e.g. Site Key, Secret Key) */
			esc_html__( 'Please enter a valid %s.', 'wp-job-openings' ),
			esc_html( $key_label )
		);

		do_action( 'awsm_jobs_captcha_validate_error', 'empty', $message, $option_name, $provider, $key_type );

		$error_code = "{$option_name}-empty-{$key_type}";

		if ( ! $this->settings_error_exists( $option_name, $error_code ) ) {
			add_settings_error( $option_name, $error_code, $message, 'error' );
		}
	}

	/**
	 * Verifies the secret key with the CAPTCHA provider's API.
	 *
	 * @param string      $value               The secret key value.
	 * @param string      $old_value           The previous value.
	 * @param string      $provider            The CAPTCHA provider.
	 * @param string      $key_type            Type of key.
	 * @param string      $option_name         The option name.
	 * @param string|null $actual_option_name  The actual option name being validated.
	 */
	private function verify_secret_key( $value, $old_value, $provider, $key_type, $option_name, $actual_option_name ) {
		$site_key = $this->get_site_key_for_verification( $provider, $actual_option_name );

		if ( '' === $site_key ) {
			return;
		}

		$verification = $this->verify_keys_with_api( $site_key, $value, $provider );

		if ( empty( $verification['valid'] ) ) {
			$this->add_api_verification_error( $verification, $option_name, $provider, $key_type );
			return;
		}

		if ( $value !== $old_value || '' === $old_value ) {
			$this->add_verification_success_message( $provider, $option_name, $value );
		}
	}

	/**
	 * Gets the site key for API verification.
	 *
	 * @param string      $provider            The CAPTCHA provider.
	 * @param string|null $actual_option_name  The actual option name being validated.
	 * @return string The site key value.
	 */
	private function get_site_key_for_verification( $provider, $actual_option_name ) {
		$site_key_field = ( 'recaptcha' === $provider && null !== $actual_option_name )
			? str_replace( 'secret_key', 'site_key', $actual_option_name )
			: self::get_captcha_data( 'field_name', $provider, 'site_key' );

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		return isset( $_POST[ $site_key_field ] )
			? sanitize_text_field( (string) $_POST[ $site_key_field ] )
			: get_option( $site_key_field, '' );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Adds API verification error message.
	 *
	 * @param array  $verification The verification result.
	 * @param string $option_name  The option name.
	 * @param string $provider     The CAPTCHA provider.
	 * @param string $key_type     Type of key.
	 */
	private function add_api_verification_error( $verification, $option_name, $provider, $key_type ) {
		$error_message = isset( $verification['message'] )
			? esc_html( $verification['message'] )
			: __( 'Unknown error', 'wp-job-openings' );

		$full_message = sprintf(
			/* translators: %s: Detailed error message returned by the CAPTCHA API */
			esc_html__( 'API Verification Failed: %s', 'wp-job-openings' ),
			$error_message
		);

		do_action( 'awsm_jobs_captcha_validate_error', 'api', $full_message, $option_name, $provider, $key_type );

		add_settings_error( $option_name, "{$option_name}-api", $full_message, 'error' );
	}

	/**
	 * Adds verification success message.
	 *
	 * @param string $provider    The CAPTCHA provider.
	 * @param string $option_name The option name.
	 * @param string $value       The validated value.
	 */
	private function add_verification_success_message( $provider, $option_name, $value ) {
		$provider_config = $this->get_provider_config( $provider );
		if ( ! empty( $provider_config['label'] ) ) {
			$service_name = $provider_config['label'];
		} else {
			$service_name = __( 'CAPTCHA', 'wp-job-openings' );
		}
		$success_code  = "{$option_name}-verified";
		$transient_key = 'awsm_captcha_success_' . md5( $option_name . $value );

		// Avoid duplicate success messages
		if ( $this->settings_error_exists( $option_name, $success_code ) || get_transient( $transient_key ) ) {
			return;
		}

		$message = sprintf(
			/* translators: %s: Service name (e.g. hCaptcha, reCAPTCHA) */
			esc_html__( '%s keys verified successfully! and settings saved.', 'wp-job-openings' ),
			esc_html( $service_name )
		);

		add_settings_error( $option_name, $success_code, $message, 'success' );
		set_transient( $transient_key, true, 5 );
	}

	/**
	 * Checks if a settings error already exists.
	 *
	 * @param string $option_name The option name.
	 * @param string $error_code  The error code to check.
	 * @return bool True if error exists.
	 */
	private function settings_error_exists( $option_name, $error_code ) {
		$existing_errors = get_settings_errors( $option_name );

		foreach ( $existing_errors as $error ) {
			if ( $error['code'] === $error_code ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sanitizes input and applies the validated value filter.
	 *
	 * This centralizes the final sanitization and filtering step.
	 *
	 * @param mixed  $input       The input to sanitize.
	 * @param string $provider    The CAPTCHA provider.
	 * @param string $key_type    Type of key.
	 * @param string $option_name The option name.
	 * @return string The sanitized and filtered value.
	 */
	private function sanitize_and_filter( $input, $provider, $key_type, $option_name ) {
		$value = ! empty( $input ) ? sanitize_text_field( trim( (string) $input ) ) : '';

		/**
		 * Filters the validated CAPTCHA key value before saving.
		 *
		 * @param string $value       The sanitized value.
		 * @param string $provider    The CAPTCHA provider.
		 * @param string $key_type    Type of key ('site_key' or 'secret_key').
		 * @param string $option_name The option name.
		 */
		return apply_filters( 'awsm_jobs_captcha_validated_value', $value, $provider, $key_type, $option_name );
	}

	/**
	 * Sanitize the new CAPTCHA enable field and sync with legacy option.
	 *
	 * @since 3.6.0
	 *
	 * @param mixed $input The input value.
	 * @return string The sanitized value.
	 */
	public function sanitize_captcha_enable( $input ) {
		$config       = self::get_captcha_config();
		$valid_values = array_keys( $config );

		$value = in_array( $input, $valid_values, true ) ? $input : 'none';

		/**
		 * Sync with legacy checkbox option for backward compatibility.
		 * Set to true if using reCAPTCHA OR if reCAPTCHA keys exist.
		 */
		if ( $value === 'recaptcha' ) {
			update_option( 'awsm_jobs_enable_recaptcha', 'enable' );
		} elseif ( $value === 'none' ) {
			update_option( 'awsm_jobs_enable_recaptcha', false );
		} else {
			$site_key   = get_option( 'awsm_jobs_recaptcha_site_key' );
			$secret_key = get_option( 'awsm_jobs_recaptcha_secret_key' );

			if ( ! empty( $site_key ) && ! empty( $secret_key ) ) {
				update_option( 'awsm_jobs_enable_recaptcha', 'enable' );
			} else {
				update_option( 'awsm_jobs_enable_recaptcha', false );
			}
		}

		/**
		 * Fire action after CAPTCHA provider change.
		 *
		 * @since 3.6.0
		 *
		 * @param string $value     The new CAPTCHA provider.
		 * @param mixed  $input     The original input.
		 * @param string $old_value The previous CAPTCHA provider.
		 */
		$old_value = $this->get_current_captcha_provider();
		if ( $value !== $old_value ) {
			do_action( 'awsm_jobs_captcha_provider_changed', $value, $input, $old_value );
		}

		/**
		 * Filter the validated CAPTCHA enable value.
		 *
		 * @since 3.6.0
		 *
		 * @param string $value The sanitized value.
		 * @param mixed  $input The original input.
		 */
		return apply_filters( 'awsm_jobs_sanitize_captcha_enable', $value, $input );
	}

}
