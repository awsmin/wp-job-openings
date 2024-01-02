<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Third_Party {
	private static $instance = null;

	protected $cpath = null;

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );

		$this->multilingual_support();

		$this->akismet_support();
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function multilingual_support() {
		// WPML and Polylang support.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'POLYLANG_VERSION' ) ) {
			require_once $this->cpath . '/translation/class-awsm-job-openings-wpml.php';
		}
	}

	public function akismet_support() {
		$is_active = awsm_jobs_is_akismet_active();
		if ( $is_active ) {
			$protection_enabled = get_option( 'awsm_jobs_enable_akismet_protection' );
			if ( $protection_enabled === 'enable' ) {
				add_action( 'awsm_job_application_submitting', array( $this, 'application_submitting_handler' ), 100 );
			}
		}
	}

	public function application_submitting_handler() {
		global $awsm_response;
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( count( $awsm_response['error'] ) === 0 && is_callable( array( 'Akismet', 'http_post' ) ) ) {
			$applicant_name  = sanitize_text_field( wp_unslash( $_POST['awsm_applicant_name'] ) );
			$applicant_email = sanitize_email( wp_unslash( $_POST['awsm_applicant_email'] ) );
			$params          = array(
				'blog'                 => get_option( 'home' ),
				'blog_lang'            => get_locale(),
				'blog_charset'         => get_option( 'blog_charset' ),
				'user_ip'              => $_SERVER['REMOTE_ADDR'],
				'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
				'referrer'             => $_SERVER['HTTP_REFERER'],
				'comment_type'         => 'application-form',
				'comment_author'       => $applicant_name,
				'comment_author_email' => $applicant_email,
			);

			$content       = '';
			$ignore_fields = array( 'awsm_applicant_name', 'awsm_applicant_email', 'awsm_job_id', 'awsm_form_privacy_policy' );
			foreach ( $_POST as $field_key => $field_val ) {
				if ( ! in_array( $field_key, $ignore_fields, true ) && strpos( $field_key, 'awsm_' ) !== false ) {
					if ( is_array( $field_val ) ) {
						$field_val = implode( ', ', awsm_jobs_array_flatten( $field_val ) );
					}
					$content .= "\n" . $field_val;
				}
			}
			$params['comment_content'] = $content;

			/**
			 * Filters the Akismet API query parameters.
			 *
			 * @since 3.2.0
			 *
			 * @param array $params Query parameters.
			 */
			$params       = apply_filters( 'awsm_jobs_akismet_query_params', $params );
			$query_string = Akismet::build_query( $params );
			$response     = Akismet::http_post( $query_string, 'comment-check' );

			// Spam job application.
			if ( $response[1] === 'true' ) {
				/**
				 * Filters the error message when Akismet marks the application as spam.
				 *
				 * @since 3.2.0
				 *
				 * @param string $err_msg Error message
				 */
				$err_msg                  = apply_filters( 'awsm_jobs_akismet_response_error', esc_html__( 'Error in submitting your application. Please refresh the page and retry.', 'wp-job-openings' ) );
				$awsm_response['error'][] = $err_msg;
			}
		}
		// phpcs:enable
	}
}

AWSM_Job_Openings_Third_Party::init();
