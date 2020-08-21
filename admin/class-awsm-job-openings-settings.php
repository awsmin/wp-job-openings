<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Settings {
	private static $instance = null;

	public function __construct( $awsm_core ) {
		$this->cpath     = untrailingslashit( plugin_dir_path( __FILE__ ) );
		$this->awsm_core = $awsm_core;
		$this->set_settings_capability();

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_settings_switch', array( $this, 'settings_switch_ajax' ) );

		add_action( 'update_option_awsm_select_page_listing', array( $this, 'update_awsm_page_listing' ), 10, 2 );
		add_action( 'update_option_awsm_permalink_slug', array( $this, 'update_awsm_permalink_slug' ), 10, 2 );
		add_action( 'update_option_awsm_hide_uploaded_files', array( $this, 'update_awsm_hide_uploaded_files' ), 10, 2 );
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
		}
		/**
		 * Filters the Settings Subtabs.
		 *
		 * @since 1.3.0
		 *
		 * @param array $subtabs Subtabs data.
		 * @param string $section Current settings section.
		 */
		return apply_filters( 'awsm_jobs_settings_subtabs', $subtabs, $section );
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
					'option_name' => 'awsm_permalink_slug',
					'callback'    => array( $this, 'sanitize_permalink_slug' ),
				),
				array(
					'option_name' => 'awsm_default_msg',
				),
				array(
					/** @since 2.0.0 */
					'option_name' => 'awsm_jobs_email_digest',
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
					'option_name' => 'awsm_jobs_listing_specs',
					'callback'    => array( $this, 'sanitize_array_fields' ),
				),
				array(
					/** @since 1.1.0 */
					'option_name' => 'awsm_jobs_details_page_template',
				),
				array(
					'option_name' => 'awsm_jobs_details_page_layout',
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
					'callback'    => 'awsm_jobs_sanitize_textarea',
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
					'callback'    => array( $this, 'sanitize_admin_from_email_id' ),
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
					'callback'    => 'awsm_jobs_sanitize_textarea',
				),
				array(
					/** @since 2.0.0 */
					'option_name' => 'awsm_jobs_notification_admin_mail_template',
				),
			),
		);
		return $settings;
	}

	private static function default_settings() {
		$options = array(
			'awsm_permalink_slug'                     => 'jobs',
			'awsm_default_msg'                        => esc_html__( 'We currently have no job openings', 'wp-job-openings' ),
			'awsm_jobs_listing_view'                  => 'list-view',
			'awsm_jobs_list_per_page'                 => 10,
			'awsm_jobs_number_of_columns'             => 3,
			'awsm_current_appearance_subtab'          => 'awsm-job-listing-nav-subtab',
			'awsm_jobs_details_page_layout'           => 'single',
			'awsm_jobs_filter'                        => array(
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
			'awsm_enable_job_filter_listing'          => 'enabled',
			'awsm_jobs_listing_available_filters'     => array( 'job-category', 'job-type', 'job-location' ),
			'awsm_jobs_listing_specs'                 => array( 'job-category', 'job-location' ),
			'awsm_jobs_admin_upload_file_ext'         => array( 'pdf', 'doc', 'docx' ),
			'awsm_enable_gdpr_cb'                     => 'true',
			'awsm_gdpr_cb_text'                       => esc_html__( 'By using this form you agree with the storage and handling of your data by this website.', 'wp-job-openings' ),
			'awsm_jobs_acknowledgement'               => 'acknowledgement',
			'awsm_jobs_notification_subject'          => 'Thanks for submitting your application for a job at {company}',
			'awsm_jobs_notification_content'          => "Dear {applicant},\n\nThis is to let you know that we have received your application.We appreciate your interest in {company} and the position of {job-title} for which you applied.  If you are selected for an interview, you can expect a phone call from our Human Resources staff shortly.\n\n Thank you, again, for your interest in our company. We do appreciate the time that you invested in this application.\n\nSincerely\n\nHR Manager\n{company}",
			'awsm_jobs_enable_admin_notification'     => 'enable',
			'awsm_jobs_admin_notification_subject'    => 'New application received for the position {job-title} [{job-id}]',
			'awsm_jobs_admin_notification_content'    => "Job Opening: {job-title} [{job-id}]\nName: {applicant}\nEmail: {applicant-email}\nPhone: {applicant-phone}\nResume: {applicant-resume}\nCover letter: {applicant-cover}\n\nPowered by WP Job Openings Plugin",
			'awsm_jobs_from_email_notification'       => get_option( 'admin_email' ),
			'awsm_jobs_admin_from_email_notification' => get_option( 'admin_email' ),
		);
		if ( ! empty( $options ) ) {
			foreach ( $options as $option => $value ) {
				if ( ! get_option( $option ) ) {
					update_option( $option, $value );
				}
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
		if ( ! empty( $page ) && is_array( $page ) ) {
			add_settings_error( 'awsm_permalink_slug', 'awsm-permalink-slug', esc_html__( 'Slug cannot be updated. A page with same slug exists. Please choose a different URL slug.', 'wp-job-openings' ) );
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

	public function validate_from_email_id( $email, $option_name ) {
		$admin_email = get_option( 'admin_email' );
		$site_domain = strtolower( $_SERVER['SERVER_NAME'] );
		if ( $this->is_localhost() ) {
			return $email;
		}

		if ( preg_match( '/^[0-9.]+$/', $site_domain ) ) {
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
				add_settings_error( $option_name, str_replace( '_', '-', $option_name ), esc_html__( "The provided 'From' email address does not belong to this site domain and may lead to issues in email delivery.", 'wp-job-openings' ), 'awsm-jobs-warning' );
				return $email;
			}
		}
		return $admin_email;
	}

	public function sanitize_from_email_id( $email ) {
		$email = sanitize_email( $email );
		return $this->validate_from_email_id( $email, 'awsm_jobs_from_email_notification' );
	}

	public function sanitize_admin_from_email_id( $email ) {
		$email = sanitize_email( $email );
		return $this->validate_from_email_id( $email, 'awsm_jobs_admin_from_email_notification' );
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
						if ( isset( $filter['tags'] ) ) {
							if ( ! empty( $filter['tags'] ) ) {
								$remove_tags = array_diff( $remove_tags, $filter['tags'] );
							}
						}
						if ( ! empty( $remove_tags ) ) {
							foreach ( $remove_tags as $remove_tag ) {
								$slug = sanitize_title( $remove_tag );
								$term = get_term_by( 'slug', $slug, $spec_key );
								if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
									wp_delete_term( $term->term_id, $spec_key );
								}
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

	public function awsm_gdpr_cb_text_handle( $input ) {
		$gdpr_enable = get_option( 'awsm_enable_gdpr_cb' );
		if ( ! empty( $gdpr_enable ) && empty( $input ) ) {
			$input = esc_html__( 'By using this form you agree with the storage and handling of your data by this website.', 'wp-job-openings' );
		}
		return htmlentities( $input, ENT_QUOTES );
	}

	public function update_awsm_page_listing( $old_value, $value ) {
		$page_id = $value;
		if ( ! empty( $page_id ) ) {
			AWSM_Job_Openings::add_shortcode_to_page( $page_id );
		}
	}

	public function update_awsm_permalink_slug( $old_value, $value ) {
		if ( empty( $value ) ) {
			update_option( 'awsm_permalink_slug', 'jobs' );
		}
		$this->awsm_core->unregister_awsm_job_openings_post_type();
		$this->awsm_core->register_post_types();
		flush_rewrite_rules();
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
			<div class="awsm-nav-subtab-container clearfix">
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

	public function display_settings_fields( $settings_fields, $container = 'table', $echo = true ) {
		$content = '';
		if ( ! empty( $settings_fields ) && is_array( $settings_fields ) ) {
			$allowed_html = array(
				'br'     => array(),
				'em'     => array(),
				'span'   => array(),
				'strong' => array(),
				'small'  => array(),
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

				$field_type = isset( $field_details['type'] ) ? $field_details['type'] : 'text';
				$label      = isset( $field_details['label'] ) ? $field_details['label'] : '';

				if ( $field_type !== 'title' ) {
					$class_name  = isset( $field_details['class'] ) ? $field_details['class'] : 'regular-text';
					$class_attr  = ! empty( $class_name ) ? sprintf( ' class="%s"', esc_attr( $class_name ) ) : '';
					$value       = isset( $field_details['value'] ) ? $field_details['value'] : '';
					$description = isset( $field_details['description'] ) ? $field_details['description'] : '';
					$help_button = isset( $field_details['help_button'] ) && is_array( $field_details['help_button'] ) ? $field_details['help_button'] : '';

					$field_label = '';
					if ( $field_type === 'checkbox' ) {
						$field_label = esc_html( $label );
					} else {
						$field_label = sprintf( '<label for="%2$s">%1$s</label>', esc_html( $label ), esc_attr( $id ) );
					}
					$field_content = '';
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
									if ( $field_type === 'checkbox' || $field_type === 'radio' ) {
										if ( is_array( $value ) ) {
											$choice_attrs .= ' ' . checked( in_array( $choice, $value ), true, false );
										} else {
											$choice_attrs .= ' ' . checked( $value, $choice, false );
										}
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
							$field_content = sprintf( '<input type="%1$s" name="%2$s" id="%3$s" value="%4$s"%5$s />', esc_attr( $field_type ), esc_attr( $field_name ), esc_attr( $id ), esc_attr( $value ), $extra_attrs );
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

		$spec_title    = $row_data = $del_btn_data = $icon_option = $tag_options = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
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
						$icon_option = sprintf( '<option value="%1$s" selected><i class="awsm-job-icon-%1$s"></i> %1$s</option>', esc_attr( $filter['icon'] ) );
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
					$tag_options .= sprintf( '<option value="%1$s" selected>%1$s</option>', esc_attr( $term->name ) );
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
				'{applicant}'        => __( 'Applicant Name:', 'wp-job-openings' ),
				'{application-id}'   => __( 'Application ID:', 'wp-job-openings' ),
				'{applicant-email}'  => __( 'Applicant Email:', 'wp-job-openings' ),
				'{applicant-phone}'  => __( 'Applicant Phone:', 'wp-job-openings' ),
				'{applicant-resume}' => __( 'Applicant Resume:', 'wp-job-openings' ),
				'{applicant-cover}'  => __( 'Cover letter:', 'wp-job-openings' ),
				'{job-title}'        => __( 'Job Title:', 'wp-job-openings' ),
				'{job-id}'           => __( 'Job ID:', 'wp-job-openings' ),
				'{job-expiry}'       => __( 'Job Expiry Date:', 'wp-job-openings' ),
				'{admin-email}'      => __( 'Site admin email:', 'wp-job-openings' ),
				'{hr-email}'         => __( 'HR Email:', 'wp-job-openings' ),
				'{company}'          => __( 'Company Name:', 'wp-job-openings' ),
			)
		);
		if ( get_option( 'awsm_hide_uploaded_files' ) === 'hide_files' ) {
			unset( $template_tags['{applicant-resume}'] );
		}
		return $template_tags;
	}
}
