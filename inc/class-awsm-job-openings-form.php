<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Form {
	private static $instance = null;

	protected $cpath = null;

	public $form_fields_order = array( 'awsm_applicant_name', 'awsm_applicant_email', 'awsm_applicant_phone', 'awsm_applicant_letter', 'awsm_file' );

	public static $allowed_html = array(
		'a'      => array(
			'href'  => array(),
			'title' => array(),
		),
		'br'     => array(),
		'em'     => array(),
		'span'   => array(),
		'strong' => array(),
		'small'  => array(),
	);

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );
		add_action( 'awsm_application_form_init', array( $this, 'application_form' ) );
		add_action( 'awsm_application_form_field_init', array( $this, 'form_field_init' ) );
		add_action( 'awsm_application_form_field_init', array( $this, 'form_language_field' ), 100 );
		add_action( 'awsm_job_application_submitting', array( $this, 'set_form_language' ) );
		add_action( 'before_awsm_job_details', array( $this, 'insert_application' ) );
		add_action( 'wp_ajax_awsm_applicant_form_submission', array( $this, 'ajax_handle' ) );
		add_action( 'wp_ajax_nopriv_awsm_applicant_form_submission', array( $this, 'ajax_handle' ) );

		add_filter( 'wp_check_filetype_and_ext', array( $this, 'check_filetype_and_ext' ), 10, 5 );
		add_action( 'add_attachment', array( $this, 'add_index_php_to_folders' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function get_allowed_html() {
		/**
		 * Filters the allowed HTML elements and attributes for the form.
		 *
		 * @since 3.0.0
		 *
		 * @param array|string $allowed_html An array of allowed HTML elements and attributes or a context name.
		 */
		return apply_filters( 'awsm_application_form_allowed_html', self::$allowed_html );
	}

	public function dynamic_form_fields( $form_attrs ) {
		$allowed_file_types   = get_option( 'awsm_jobs_admin_upload_file_ext' );
		$allowed_file_content = '';
		if ( is_array( $allowed_file_types ) && ! empty( $allowed_file_types ) ) {
			$allowed_file_types = '.' . join( ', .', $allowed_file_types );
			/* translators: %1$s: comma-separated list of allowed file types */
			$allowed_file_content = '<small>' . sprintf( esc_html__( 'Allowed Type(s): %1$s', 'wp-job-openings' ), $allowed_file_types ) . '</small>';
		}

		$default_form_fields = array(
			'awsm_applicant_name'   => array(
				'label' => __( 'Full Name', 'wp-job-openings' ),
				'id'    => 'awsm-applicant-name',
				'class' => array( 'awsm-job-form-control' ),
			),

			'awsm_applicant_email'  => array(
				'label'      => __( 'Email', 'wp-job-openings' ),
				'field_type' => array(
					'tag'  => 'input',
					'type' => 'email',
				),
				'id'         => 'awsm-applicant-email',
				'class'      => array( 'awsm-job-form-control' ),
				'error'      => array(
					'error_rule' => 'email',
					'error_msg'  => __( 'Please enter a valid email address.', 'wp-job-openings' ),
				),
			),

			'awsm_applicant_phone'  => array(
				'label'      => __( 'Phone', 'wp-job-openings' ),
				'field_type' => array(
					'tag'  => 'input',
					'type' => 'tel',
				),
				'id'         => 'awsm-applicant-phone',
				'class'      => array( 'awsm-job-form-control' ),
				'error'      => array(
					'error_msg' => __( 'Please enter a valid phone number.', 'wp-job-openings' ),
				),
			),

			'awsm_applicant_letter' => array(
				'label'      => __( 'Cover Letter', 'wp-job-openings' ),
				'field_type' => array(
					'tag' => 'textarea',
				),
				'id'         => 'awsm-cover-letter',
				'class'      => array( 'awsm-job-form-control' ),
			),

			'awsm_file'             => array(
				'label'      => __( 'Upload CV/Resume', 'wp-job-openings' ),
				'field_type' => array(
					'tag'    => 'input',
					'type'   => 'file',
					'accept' => $allowed_file_types,
				),
				'id'         => 'awsm-application-file',
				'class'      => array( 'awsm-resume-file-control', 'awsm-job-form-control', 'awsm-form-file-control' ),
				'content'    => $allowed_file_content,
			),
		);
		/**
		 * Filters the job application form fields.
		 *
		 * @since 1.0.0
		 * @since 2.2.1 The `$form_attrs` parameter was added.
		 *
		 * @param array $form_fields Form fields array.
		 * @param array $form_attrs Attributes array for the form.
		 */
		$form_fields = apply_filters( 'awsm_application_form_fields', $default_form_fields, $form_attrs );
		return $form_fields;
	}

	public function display_dynamic_fields( $form_attrs ) {
		$dynamic_form_fields = $this->dynamic_form_fields( $form_attrs );
		if ( ! empty( $dynamic_form_fields ) ) {
			$ordered_form_fields = array();
			/**
			 * Filters the job application form fields order.
			 *
			 * @since 1.2.0
			 * @since 2.2.1 The `$form_attrs` parameter was added.
			 *
			 * @param array $form_fields_order Form fields array.
			 * @param array $form_attrs Attributes array for the form.
			 */
			$form_fields_order = apply_filters( 'awsm_application_form_fields_order', $this->form_fields_order, $form_attrs );
			foreach ( $form_fields_order as $form_field_order ) {
				$ordered_form_fields[ $form_field_order ] = $dynamic_form_fields[ $form_field_order ];
			}
			$dynamic_form_fields = $ordered_form_fields;
			$allowed_html        = self::get_allowed_html();
			$required_msg        = esc_attr__( 'This field is required.', 'wp-job-openings' );
			$form_output         = '';
			foreach ( $dynamic_form_fields as $field_name => $field_args ) {
				$show_field = ( isset( $field_args['show_field'] ) ) ? $field_args['show_field'] : true;
				if ( $show_field ) {
					$label            = ( isset( $field_args['label'] ) ) ? $field_args['label'] : '';
					$tag              = ( isset( $field_args['field_type'] ) ) ? ( ( isset( $field_args['field_type']['tag'] ) ) ? $field_args['field_type']['tag'] : 'input' ) : 'input';
					$input_type       = ( isset( $field_args['field_type'] ) ) ? ( ( isset( $field_args['field_type']['type'] ) ) ? $field_args['field_type']['type'] : 'text' ) : 'text';
					$field_id         = ( isset( $field_args['id'] ) ) ? $field_args['id'] : $field_name;
					$field_class      = 'awsm-job-form-field';
					$field_class      = ( isset( $field_args['class'] ) && is_array( $field_args['class'] ) ) ? $field_class . ' ' . join( ' ', $field_args['class'] ) : $field_class;
					$required         = ( isset( $field_args['required'] ) ) ? $field_args['required'] : true;
					$required_attr    = ( $required ) ? ' required' : '';
					$data_required    = ( $required ) ? ' data-msg-required="' . $required_msg . '"' : '';
					$required_label   = ( $required ) ? ' <span class="awsm-job-form-error">*</span>' : '';
					$form_group_class = 'awsm-job-form-group';
					$form_group_class = ( isset( $field_args['label_inline'] ) ) ? $form_group_class . ' awsm-job-inline-group' : $form_group_class;
					$extra_content    = ( isset( $field_args['content'] ) ) ? $field_args['content'] : '';
					// Validation
					$data_error_msg = ( isset( $field_args['error'] ) && ! empty( $field_args['error'] ) ) ? ( ( isset( $field_args['error']['error_msg'], $field_args['error']['error_rule'] ) && ! empty( $field_args['error']['error_msg'] ) && ! empty( $field_args['error']['error_rule'] ) ) ? ( ' data-rule-' . $field_args['error']['error_rule'] . '="true" data-msg-' . $field_args['error']['error_rule'] . '="' . esc_attr( $field_args['error']['error_msg'] ) . '"' ) : '' ) : '';
					// Common attributes for tags
					$common_attrs = sprintf( 'name="%1$s" class="%2$s" id="%3$s"%4$s', esc_attr( $field_name ), esc_attr( $field_class ), esc_attr( $field_id ), $required_attr . $data_required . $data_error_msg );
					if ( $input_type === 'file' ) {
						$common_attrs .= isset( $field_args['field_type']['accept'] ) ? sprintf( ' accept="%s"', esc_attr( $field_args['field_type']['accept'] ) ) : '';
					} else {
						if ( $tag !== 'textarea' && $tag !== 'select' ) {
							$common_attrs .= isset( $field_args['field_type']['value'] ) ? sprintf( ' value="%s"', esc_attr( $field_args['field_type']['value'] ) ) : '';
						}
					}

					$field_content = $label_content = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					if ( ! empty( $label ) ) {
						$label_content = sprintf( '<label for="%2$s">%1$s</label>', wp_kses( $label, $allowed_html ) . $required_label, esc_attr( $field_id ) );
					}
					if ( $tag === 'input' || $tag === 'select' ) {
						if ( $tag === 'select' || $input_type === 'checkbox' || $input_type === 'radio' ) {
							$options = isset( $field_args['field_type']['options'] ) ? $field_args['field_type']['options'] : '';
							if ( ! empty( $options ) && is_array( $options ) ) {
								$options_content = '';
								if ( $tag === 'select' ) {
									if ( ! $required ) {
										$options_content .= sprintf( '<option value="">%s</option>', esc_html__( '--Please Choose an Option--', 'wp-job-openings' ) );
									}
									foreach ( $options as $option ) {
										$options_content .= sprintf( '<option value="%s">%s</option>', esc_attr( $option ), esc_html( $option ) );
									}
									$field_content .= sprintf( '<select %2$s>%1$s</select>', $options_content, $common_attrs );
								} else {
									$id_suffix = 1;
									foreach ( $options as $option ) {
										$name_suffix      = ( $input_type === 'checkbox' ) ? '[]' : '';
										$current_field_id = esc_attr( $field_id . '_' . $id_suffix );
										$common_attrs     = sprintf( 'name="%1$s" class="%2$s" id="%3$s"%4$s', esc_attr( $field_name . $name_suffix ), esc_attr( $field_class ), $current_field_id, $required_attr . $data_required . $data_error_msg );
										$options_content .= sprintf( '<span><input type="%s" value="%s" %s /> <label for="%s">%s</label></span>', esc_attr( $input_type ), esc_attr( $option ), $common_attrs, $current_field_id, esc_html( $option ) );
										$id_suffix ++;
									}
									$field_content .= sprintf( '<div class="awsm-job-form-options-container">%s</div>', $options_content );
								}
							}
						} else {
							$field_content .= sprintf( '<input type="%1$s" %2$s />', esc_attr( $input_type ), $common_attrs );
						}
					} elseif ( $tag === 'textarea' ) {
						$field_content .= sprintf( '<textarea %1$s rows="5" cols="50"></textarea>', $common_attrs );
					}
					if ( isset( $field_args['label_inline'] ) && $field_args['label_inline'] === 'right' ) {
						$field_content .= $label_content;
					} else {
						$field_content = $label_content . $field_content;
					}
					$field_type = $tag === 'input' ? $input_type : $tag;
					/**
					 * Filters the field content of a specific field type of the job application form.
					 *
					 * @since 2.0.0
					 * @since 2.2.1 The `$form_attrs` parameter was added.
					 *
					 * @param string $field_content The content.
					 * @param array $field_args Form field options.
					 * @param array $form_attrs Attributes array for the form.
					 */
					$field_content = apply_filters( "awsm_application_dynamic_form_{$field_type}_field_content", $field_content, $field_args, $form_attrs );
					$field_output  = sprintf( '<div class="%2$s">%1$s</div>', $field_content . wp_kses( $extra_content, $allowed_html ), esc_attr( $form_group_class ) );
					/**
					 * Filters the form field content of the job application form.
					 *
					 * @since 2.0.0
					 * @since 2.2.1 The `$form_attrs` parameter was added.
					 *
					 * @param string $field_output The content.
					 * @param string $field_type The field type.
					 * @param array $field_args Form field options.
					 * @param array $form_attrs Attributes array for the form.
					 */
					$form_output .= apply_filters( 'awsm_application_dynamic_form_field_content', $field_output, $field_type, $field_args, $form_attrs );
				}
			}
			/**
			 * Filters the dynamic form fields content of the job application form.
			 *
			 * @since 1.3.0
			 * @since 2.2.1 The `$form_attrs` parameter was added.
			 *
			 * @param string $form_output The content.
			 * @param array $dynamic_form_fields Dynamic form fields.
			 * @param array $form_attrs Attributes array for the form.
			 */
			echo apply_filters( 'awsm_application_dynamic_form_fields_content', $form_output, $dynamic_form_fields, $form_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function get_gdpr_field_label() {
		$gdpr_enable  = get_option( 'awsm_enable_gdpr_cb' );
		$gdpr_cb_text = get_option( 'awsm_gdpr_cb_text' );
		if ( ! empty( $gdpr_enable ) && ! empty( $gdpr_cb_text ) ) {
			return $gdpr_cb_text;
		} else {
			return false;
		}
	}

	public function display_gdpr_field( $form_attrs ) {
		$label = $this->get_gdpr_field_label();
		if ( ! empty( $label ) ) {
			$field_id      = $form_attrs['single_form'] ? 'awsm_form_privacy_policy' : esc_attr( 'awsm_form_privacy_policy-' . $form_attrs['job_id'] );
			$field_content = sprintf( '<div class="awsm-job-form-group awsm-job-inline-group"><input name="awsm_form_privacy_policy" class="awsm-job-form-field" id="%1$s" value="yes" type="checkbox" data-msg-required="%3$s" aria-required="true" required><label for="%1$s">%2$s <span class="awsm-job-form-error">*</span></label></div>', esc_attr( $field_id ), wp_kses( $label, self::get_allowed_html() ), esc_attr__( 'This field is required.', 'wp-job-openings' ) );
			/**
			 * Filters the privacy policy checkbox field content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $field_content Field HTML content.
			 * @param array $form_attrs Attributes array for the form.
			 */
			echo apply_filters( 'awsm_application_form_gdpr_field_content', $field_content, $form_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function display_recaptcha_field( $form_attrs ) {
		if ( $this->is_recaptcha_set() ) :
			/**
			 * Filters the reCAPTCHA visibility in the application form.
			 *
			 * @since 2.2.0
			 * @since 2.2.1 The `$form_attrs` parameter was added.
			 *
			 * @param bool $is_visible Whether the reCAPTCHA is visible or not in the form.
			 * @param array $form_attrs Attributes array for the form.
			 */
			$is_visible = apply_filters( 'awsm_application_form_is_recaptcha_visible', true, $form_attrs );

			if ( $is_visible ) :
				$site_key     = get_option( 'awsm_jobs_recaptcha_site_key' );
				$fallback_url = add_query_arg( 'k', $site_key, 'https://www.google.com/recaptcha/api/fallback' );
				?>
				<div class="awsm-job-form-group awsm-job-g-recaptcha-group">
					<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
					<noscript>
						<div style="width: 302px; height: 422px; position: relative;">
							<div style="width: 302px; height: 422px; position: absolute;">
								<iframe src="<?php echo esc_url( $fallback_url ); ?>" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
							</div>
							<div style="width: 300px; height: 60px; border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
								<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
							</div>
						</div>
					</noscript>
				</div>
				<?php
			endif;
		endif;
	}

	public function application_form() {
		$form_attrs = array(
			'single_form' => true,
			'job_id'      => get_the_ID(),
		);
		include AWSM_Job_Openings::get_template_path( 'form.php', 'single-job' );
	}

	public function form_field_init( $form_attrs ) {
		$this->display_dynamic_fields( $form_attrs );
		$this->display_gdpr_field( $form_attrs );
		if ( awsm_jobs_is_new_captcha_enabled() ) {
			$this->display_captcha_field( $form_attrs );
		} else {
			$this->display_recaptcha_field( $form_attrs );
		}

	}

	public function check_filetype_and_ext( $wp_filetype, $file, $filename, $mimes, $real_mime = '' ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $real_mime ) && isset( $_POST['action'] ) && $_POST['action'] === 'awsm_applicant_form_submission' && empty( $wp_filetype['type'] ) && ! empty( $mimes ) ) {
			$filetype = wp_check_filetype( $filename, $mimes );
			// fix issue with application/vnd.openxmlformats-officedocument.* mime types in some PHP versions.
			$extensions = array( 'docx', 'dotx', 'xlsx', 'xltx', 'pptx', 'ppsx', 'potx', 'sldx' );
			if ( $filetype['ext'] && in_array( $filetype['ext'], $extensions, true ) && $real_mime === $filetype['type'] . $filetype['type'] ) {
				$wp_filetype['ext']  = $filetype['ext'];
				$wp_filetype['type'] = $filetype['type'];
			}
		}
		return $wp_filetype;
	}

	public function upload_dir( $param ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'awsm_applicant_form_submission' ) {
			$subdir = '/' . AWSM_JOBS_UPLOAD_DIR_NAME;
			if ( empty( $param['subdir'] ) ) {
				$param['path']   = $param['path'] . $subdir;
				$param['url']    = $param['url'] . $subdir;
				$param['subdir'] = $subdir;
			} else {
				$subdir         .= $param['subdir'];
				$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
				$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
				$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
			}
		}
		return $param;
	}

	public function add_index_php_to_folders( $attachment_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'awsm_applicant_form_submission' ) {
			$file_path = get_attached_file( $attachment_id );
			if ( strpos( $file_path, AWSM_JOBS_UPLOAD_DIR_NAME ) !== false ) {
				$directory_path = dirname( $file_path );
				$index_php_file = $directory_path . '/index.php';
				if ( ! file_exists( $index_php_file ) ) {
					$index_php_content = '<?php\n\n//Silence is golden.\n';
					file_put_contents( $index_php_file, $index_php_content );
				}
			}
		}
	}

	public function hashed_file_name( $dir, $name, $ext ) {
		if ( function_exists( 'random_bytes' ) ) {
			$random_bytes = bin2hex( random_bytes( 16 ) );
		} else {
			$random_bytes = uniqid( wp_rand(), true ) . uniqid( wp_rand(), true );
		}
		$file_name = hash( 'sha256', $name . $random_bytes . time() );
		return sanitize_file_name( $file_name . $ext );
	}

	public function form_language_field() {
		$current_lang = AWSM_Job_Openings::get_current_language();
		if ( ! empty( $current_lang ) ) {
			printf( '<input type="hidden" name="lang" value="%s">', esc_attr( $current_lang ) );
		}
	}

	public function set_form_language() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['lang'] ) ) {
			AWSM_Job_Openings::set_current_language( $_POST['lang'] );
		}
		// phpcs:enable
	}

	public function insert_application() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		global $awsm_response;

		$awsm_response = array(
			'success' => array(),
			'error'   => array(),
		);

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['action'] ) && $_POST['action'] === 'awsm_applicant_form_submission' ) {
			$job_id               = intval( $_POST['awsm_job_id'] );
			$job_status           = get_post_status( $job_id );
			$applicant_name       = sanitize_text_field( wp_unslash( $_POST['awsm_applicant_name'] ) );
			$applicant_email      = sanitize_email( wp_unslash( $_POST['awsm_applicant_email'] ) );
			$applicant_phone      = sanitize_text_field( wp_unslash( $_POST['awsm_applicant_phone'] ) );
			$applicant_letter     = awsm_jobs_sanitize_textarea( wp_unslash( $_POST['awsm_applicant_letter'] ) );
			$attachment           = isset( $_FILES['awsm_file'] ) ? $_FILES['awsm_file'] : '';
			$agree_privacy_policy = false;
			$generic_err_msg      = esc_html__( 'Error in submitting your application. Please refresh the page and retry.', 'wp-job-openings' );

			// Handle CAPTCHA validation.
			if ( awsm_jobs_is_new_captcha_enabled() || $this->is_captcha_set() ) {
				$awsm_response = $this->validate_captcha_submission( $awsm_response );
			} elseif ( $this->is_recaptcha_set() ) {
				$is_human = false;

				if ( ! empty( $_POST['g-recaptcha-response'] ) ) {
					$is_human = $this->validate_captcha_field( wp_unslash( $_POST['g-recaptcha-response'] ) );
				}
				if ( ! $is_human ) {
					$awsm_response['error'][] = esc_html__(
						'Please verify that you are not a robot.',
						'wp-job-openings'
					);
				}
			}
			if ( $this->get_gdpr_field_label() !== false ) {
				if ( ! isset( $_POST['awsm_form_privacy_policy'] ) || $_POST['awsm_form_privacy_policy'] !== 'yes' ) {
					$awsm_response['error'][] = esc_html__( 'Please agree to our privacy policy.', 'wp-job-openings' );
				} else {
					$agree_privacy_policy = sanitize_text_field( $_POST['awsm_form_privacy_policy'] );
				}
			}
			if ( get_post_type( $job_id ) !== 'awsm_job_openings' ) {
				$awsm_response['error'][] = esc_html__( 'Error occurred: Invalid Job.', 'wp-job-openings' );
			}
			if ( get_post_status( $job_id ) === 'expired' ) {
				$awsm_response['error'][] = esc_html__( 'Sorry! This job has expired.', 'wp-job-openings' );
			}
			if ( empty( $applicant_name ) ) {
				$awsm_response['error'][] = esc_html__( 'Name is required.', 'wp-job-openings' );
			}
			if ( empty( $applicant_email ) ) {
				$awsm_response['error'][] = esc_html__( 'Email is required.', 'wp-job-openings' );
			} else {
				if ( ! filter_var( $applicant_email, FILTER_VALIDATE_EMAIL ) ) {
					$awsm_response['error'][] = esc_html__( 'Invalid email format.', 'wp-job-openings' );
				}
			}
			if ( empty( $applicant_phone ) ) {
				$awsm_response['error'][] = esc_html__( 'Contact number is required.', 'wp-job-openings' );
			} else {
				if ( ! preg_match( '%^[+]?[0-9()/ -]*$%', trim( $applicant_phone ) ) ) {
					$awsm_response['error'][] = esc_html__( 'Invalid phone number.', 'wp-job-openings' );
				}
			}
			if ( empty( $applicant_letter ) ) {
				$awsm_response['error'][] = esc_html__( 'Cover Letter cannot be empty.', 'wp-job-openings' );
			}
			if ( empty( $attachment ) || ! isset( $attachment['error'] ) || $attachment['error'] > 0 ) {
				$awsm_response['error'][] = esc_html__( 'Please select your cv/resume.', 'wp-job-openings' );
			}
			if ( $job_status !== 'publish' ) {
				$awsm_response['error'][] = esc_html__( 'Private job submission is not allowed.', 'wp-job-openings' );
			}

			/**
			 * Fires before job application submission
			 *
			 * @since 1.2
			 */
			do_action( 'awsm_job_application_submitting' );

			if ( count( $awsm_response['error'] ) === 0 ) {
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					include ABSPATH . 'wp-admin/includes/file.php';
				}
				if ( ! function_exists( 'wp_crop_image' ) ) {
					include ABSPATH . 'wp-admin/includes/image.php';
				}
				$mimes              = array();
				$allowed_mime_types = get_allowed_mime_types();
				$alowed_types       = get_option( 'awsm_jobs_admin_upload_file_ext' );
				foreach ( $alowed_types as $allowed_type ) {
					if ( isset( $allowed_mime_types[ $allowed_type ] ) ) {
						$mimes[ $allowed_type ] = $allowed_mime_types[ $allowed_type ];
					}
				}
				$override = array(
					'test_form'                => false,
					'mimes'                    => $mimes,
					'unique_filename_callback' => array( $this, 'hashed_file_name' ),
				);
				add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
				$movefile = wp_handle_upload( $attachment, $override );
				remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );
				if ( $movefile && ! isset( $movefile['error'] ) ) {
					$post_base_data   = array(
						'post_title'     => $applicant_name,
						'post_content'   => '',
						'post_status'    => 'publish',
						'comment_status' => 'closed',
					);
					$application_data = array_merge(
						$post_base_data,
						array(
							'post_type'   => 'awsm_job_application',
							'post_parent' => $job_id,
						)
					);
					$application_id   = wp_insert_post( $application_data, true );

					if ( ! is_wp_error( $application_id ) ) {
						$attachment_data = array_merge(
							$post_base_data,
							array(
								'post_mime_type' => $movefile['type'],
								'guid'           => $movefile['url'],
							)
						);
						$attach_id       = wp_insert_attachment( $attachment_data, $movefile['file'], $application_id, true );

						if ( ! is_wp_error( $attach_id ) ) {
							$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
							wp_update_attachment_metadata( $attach_id, $attach_data );
							$applicant_details = array(
								'awsm_job_id'           => $job_id,
								'awsm_apply_for'        => html_entity_decode( esc_html( get_the_title( $job_id ) ) ),
								'awsm_applicant_ip'     => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '',
								'awsm_applicant_name'   => $applicant_name,
								'awsm_applicant_email'  => $applicant_email,
								'awsm_applicant_phone'  => $applicant_phone,
								'awsm_applicant_letter' => $applicant_letter,
								'awsm_attachment_id'    => $attach_id,
							);
							if ( ! empty( $agree_privacy_policy ) ) {
								$applicant_details['awsm_agree_privacy_policy'] = $agree_privacy_policy;
							}
							foreach ( $applicant_details as $meta_key => $meta_value ) {
								update_post_meta( $application_id, $meta_key, $meta_value );
							}
							// Now, send notification email
							$applicant_details['application_id'] = $application_id;
							$this->notification_email( $applicant_details );

							$awsm_response['success'][] = esc_html__( 'Your application has been submitted.', 'wp-job-openings' );

							/**
							 * Fires after successful job application submission
							 *
							 * @since 1.2
							 *
							 * @param int $application_id Application ID
							 */
							do_action( 'awsm_job_application_submitted', $application_id );

						} else {
							AWSM_Job_Openings::log( $attach_id );
							$awsm_response['error'][] = $generic_err_msg;
						}
					} else {
						AWSM_Job_Openings::log( $application_id );
						$awsm_response['error'][] = $generic_err_msg;
					}
				} else {
					AWSM_Job_Openings::log( $movefile );
					$awsm_response['error'][] = $movefile['error'];
				}
			}
			add_action( 'awsm_application_form_notices', array( $this, 'awsm_form_submit_notices' ) );
		}
		return $awsm_response;
		// phpcs:enable
	}

	public function is_recaptcha_set() {
		$is_set           = false;
		$enable_recaptcha = get_option( 'awsm_jobs_enable_recaptcha' );
		$site_key         = get_option( 'awsm_jobs_recaptcha_site_key' );
		$secret_key       = get_option( 'awsm_jobs_recaptcha_secret_key' );
		if ( $enable_recaptcha === 'enable' && ! empty( $site_key ) && ! empty( $secret_key ) ) {
			$is_set = true;
		}
		return $is_set;
	}

	public function get_recaptcha_response( $token ) {
		$result     = array();
		$secret_key = get_option( 'awsm_jobs_recaptcha_secret_key' );
		$response   = wp_safe_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => $secret_key,
					'response' => $token,
					'remoteip' => $_SERVER['REMOTE_ADDR'],
				),
			)
		);
		if ( ! is_wp_error( $response ) ) {
			$response_body = wp_remote_retrieve_body( $response );
			if ( '' !== $response_body ) {
				if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
					$result = json_decode( $response_body, true );
				}
			}
		}
		return $result;
	}

	public function validate_captcha_field( $token ) {
		$is_valid = false;
		if ( ! empty( $token ) ) {
			$result = $this->get_recaptcha_response( $token );
			if ( ! empty( $result ) ) {
				$is_valid = isset( $result['success'] ) && $result['success'] === true;
			}
		}
		return $is_valid;
	}

	public function ajax_handle() {
		$response = $this->insert_application();
		wp_send_json( $response );
	}

	public function awsm_form_submit_notices() {
		global $awsm_response;
		$msg_array  = array();
		$class_name = 'awsm-default-message';
		$content    = '';
		if ( ! empty( $awsm_response['success'] ) ) {
			$msg_array  = $awsm_response['success'];
			$class_name = 'awsm-success-message';
		} else {
			if ( ! empty( $awsm_response['error'] ) ) {
				$msg_array  = $awsm_response['error'];
				$class_name = 'awsm-error-message';
				$content   .= '<p>' . esc_html__( 'The following errors have occurred:', 'wp-job-openings' ) . '</p>';
			}
		}
		foreach ( $msg_array as $msg ) {
			$content .= '<li>' . esc_html( $msg ) . '</li>';
		}
		printf( '<ul class="%1$s">%2$s</ul>', esc_attr( $class_name ), $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function get_mail_template_tags( $applicant_details, $options = array() ) {
		$job_expiry     = get_post_meta( $applicant_details['awsm_job_id'], 'awsm_job_expiry', true );
		$job_expiry     = ( ! empty( $job_expiry ) ) ? date_i18n( get_awsm_jobs_date_format( 'expiry-mail' ), strtotime( $job_expiry ) ) : '';
		$attachment_url = isset( $applicant_details['awsm_attachment_id'] ) ? wp_get_attachment_url( $applicant_details['awsm_attachment_id'] ) : '';
		if ( ! empty( $attachment_url ) && get_option( 'awsm_hide_uploaded_files' ) === 'hide_files' ) {
			$attachment_url = AWSM_Job_Openings::get_application_edit_link( $applicant_details['application_id'] );
		}
		$author_id    = get_post_field( 'post_author', $applicant_details['awsm_job_id'] );
		$author_email = get_the_author_meta( 'user_email', intval( $author_id ) );
		$tags         = array(
			'{applicant}'        => $applicant_details['awsm_applicant_name'],
			'{application-id}'   => $applicant_details['application_id'],
			'{applicant-email}'  => $applicant_details['awsm_applicant_email'],
			'{applicant-phone}'  => isset( $applicant_details['awsm_applicant_phone'] ) ? $applicant_details['awsm_applicant_phone'] : '',
			'{job-id}'           => $applicant_details['awsm_job_id'],
			'{job-expiry}'       => $job_expiry,
			'{job-title}'        => $applicant_details['awsm_apply_for'],
			'{applicant-cover}'  => isset( $applicant_details['awsm_applicant_letter'] ) ? nl2br( $applicant_details['awsm_applicant_letter'] ) : '',
			'{applicant-resume}' => ( ! empty( $attachment_url ) ) ? esc_url( $attachment_url ) : '',
			'{author-email}'     => esc_html( $author_email ),
		);

		$tags = array_merge( $tags, AWSM_Job_Openings::get_mail_generic_template_tags( $options ) );

		/**
		 * Filters the mail template tags.
		 *
		 * @since 1.4
		 *
		 * @param array $tags Mail template tags
		 * @param array $applicant_details Applicant Details
		 * @param array $options Settings values
		 */
		return apply_filters( 'awsm_jobs_mail_template_tags', $tags, $applicant_details, $options );
	}

	public static function get_expired_notification_content() {
		$options = array(
			'enable'  => 'enable',
			'to'      => '{author-email}',
			'subject' => 'Job Listing Expired',
			'content' => "This email is to notify you that your job listing for [{job-title}] has just expired. As a result, applicants will no longer be able to apply for this position.\n\nIf you would like to extend the expiration date or remove the listing, please log in to the dashboard and take the necessary steps.\n\nPowered by WP Job Openings Plugin",
		);
		return $options;
	}

	/**
	 * Get the notification options.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Notification type - applicant or admin
	 * @return array
	 */
	public static function get_notification_options( $type ) {
		$options         = array();
		$admin_email     = get_option( 'admin_email' );
		$hr_email        = get_option( 'awsm_hr_email_address' );
		$expired_options = self::get_expired_notification_content();
		if ( ! class_exists( 'AWSM_Job_Openings_Settings' ) ) {
			require_once AWSM_JOBS_PLUGIN_DIR . '/admin/class-awsm-job-openings-settings.php';
		}
		$default_from_email = AWSM_Job_Openings_Settings::awsm_from_email();

		if ( $type === 'applicant' ) {
			$options = array(
				'acknowledgement' => get_option( 'awsm_jobs_acknowledgement' ),
				'from'            => get_option( 'awsm_jobs_from_email_notification', '{default-from-email}' ),
				'reply_to'        => get_option( 'awsm_jobs_reply_to_notification' ),
				'cc'              => get_option( 'awsm_jobs_hr_notification', $hr_email ),
				'subject'         => get_option( 'awsm_jobs_notification_subject', '' ),
				'content'         => get_option( 'awsm_jobs_notification_content', '' ),
				'html_template'   => get_option( 'awsm_jobs_notification_mail_template' ),
			);
		} elseif ( $type === 'admin' ) {
			$options = array(
				'enable'        => get_option( 'awsm_jobs_enable_admin_notification' ),
				'from'          => get_option( 'awsm_jobs_admin_from_email_notification', '{default-from-email}' ),
				'reply_to'      => get_option( 'awsm_jobs_admin_reply_to_notification', '{applicant-email}' ),
				'to'            => get_option( 'awsm_jobs_admin_to_notification', $hr_email ),
				'cc'            => get_option( 'awsm_jobs_admin_hr_notification' ),
				'subject'       => get_option( 'awsm_jobs_admin_notification_subject', '' ),
				'content'       => get_option( 'awsm_jobs_admin_notification_content', '' ),
				'html_template' => get_option( 'awsm_jobs_notification_admin_mail_template' ),
			);
		} elseif ( $type === 'author' ) {
			$options = array(
				'enable'        => get_option( 'awsm_jobs_enable_expiry_notification', $expired_options['enable'] ),
				'from'          => get_option( 'awsm_jobs_author_from_email_notification', '{default-from-email}' ),
				'reply_to'      => get_option( 'awsm_jobs_author_reply_to_notification', get_option( 'awsm_jobs_reply_to_notification' ) ),
				'to'            => get_option( 'awsm_jobs_author_to_notification', '{author-email}' ),
				'cc'            => get_option( 'awsm_jobs_author_hr_notification' ),
				'subject'       => get_option( 'awsm_jobs_author_notification_subject', $expired_options['subject'] ),
				'content'       => get_option( 'awsm_jobs_author_notification_content', $expired_options['content'] ),
				'html_template' => get_option( 'awsm_jobs_notification_author_mail_template' ),
			);
		}
		return $options;
	}

	/**
	 * Handle applicant and admin notification emails.
	 *
	 * @since 3.0.0 The `$data` parameter was added.
	 *
	 * @param array $applicant_details Applicant details.
	 * @param array $data Notification data if provided will override the default.
	 */
	protected function notification_email( $applicant_details, $data = array() ) {
		/**
		 * Filters the default notifications types - applicant or admin.
		 *
		 * @since 3.2.0
		 *
		 * @param array $types Notification types.
		 * @param array $applicant_details Applicant details.
		 * @param array $data Notification data.
		 */
		$types = apply_filters(
			'awsm_jobs_default_notifications_types',
			array( 'applicant', 'admin' ),
			$applicant_details,
			$data
		);
		foreach ( $types as $type ) {
			if ( ! isset( $data[ $type ] ) ) {
				$data[ $type ] = self::get_notification_options( $type );
			}
			$options = $data[ $type ];

			// Check if the notification is enabled or not.
			$enable = false;
			if ( $type === 'applicant' ) {
				$enable = $options['acknowledgement'] === 'acknowledgement';
			} elseif ( $type === 'admin' ) {
				$enable = $options['enable'] === 'enable';
			}

			if ( $enable ) {
				$admin_email     = get_option( 'admin_email' );
				$hr_mail         = get_option( 'awsm_hr_email_address' );
				$applicant_email = $applicant_details['awsm_applicant_email'];
				$company_name    = get_option( 'awsm_job_company_name' );
				$from            = ( ! empty( $company_name ) ) ? $company_name : get_option( 'blogname' );
				$author_id       = get_post_field( 'post_author', $applicant_details['awsm_job_id'] );
				$author_email    = get_the_author_meta( 'user_email', intval( $author_id ) );
				if ( ! class_exists( 'AWSM_Job_Openings_Settings' ) ) {
					require_once AWSM_JOBS_PLUGIN_DIR . '/admin/class-awsm-job-openings-settings.php';
				}
				$default_from_email = AWSM_Job_Openings_Settings::awsm_from_email();

				$tags             = $this->get_mail_template_tags(
					$applicant_details,
					array(
						'admin_email'        => $admin_email,
						'hr_email'           => $hr_mail,
						'company_name'       => $company_name,
						'default_from_email' => $default_from_email,
					)
				);
				$tag_names        = array_keys( $tags );
				$tag_values       = array_values( $tags );
				$email_tag_names  = array( '{admin-email}', '{hr-email}', '{applicant-email}', '{author-email}', '{default-from-email}' );
				$email_tag_values = array( $admin_email, $hr_mail, $applicant_email, $author_email, $default_from_email );

				if ( ! empty( $options['subject'] ) && ! empty( $options['content'] ) ) {
					$subject    = str_replace( $tag_names, $tag_values, $options['subject'] );
					$from_email = str_replace( $tag_names, $tag_values, $options['from'] );
					$reply_to   = str_replace( $email_tag_names, $email_tag_values, $options['reply_to'] );
					$cc         = str_replace( $email_tag_names, $email_tag_values, $options['cc'] );

					/**
					 * Filters the applicant or admin notification mail headers.
					 *
					 * @since 1.4
					 *
					 * @param array $headers Additional headers.
					 * @param array $applicant_details Applicant details.
					 */
					$headers = apply_filters(
						"awsm_jobs_{$type}_notification_mail_headers",
						array(
							'content_type' => 'Content-Type: text/html; charset=UTF-8',
							'from'         => sprintf( 'From: %1$s <%2$s>', $from, $from_email ),
							'reply_to'     => 'Reply-To: ' . $reply_to,
							'cc'           => 'Cc: ' . $cc,
						),
						$applicant_details
					);

					$reply_to = trim( str_replace( 'Reply-To:', '', $headers['reply_to'] ) );
					if ( empty( $reply_to ) ) {
						unset( $headers['reply_to'] );
					}

					$mail_cc = trim( str_replace( 'Cc:', '', $headers['cc'] ) );
					if ( empty( $mail_cc ) ) {
						unset( $headers['cc'] );
					}

					/**
					 * Filters the applicant or admin notification mail attachments.
					 *
					 * @since 1.4
					 *
					 * @param array $attachments Mail attachments.
					 * @param array $applicant_details Applicant details.
					 */
					$attachments       = apply_filters( "awsm_jobs_{$type}_notification_mail_attachments", array(), $applicant_details );
					$admin_attachments = $attachments;
					if ( $type === 'admin' ) {
						$attachments = ! empty( $attachments ) ? wp_list_pluck( $attachments, 'file' ) : array();
					}

					$mail_content = AWSM_Job_Openings_Mail_Customizer::sanitize_content( $options['content'] );
					if ( stripos( $mail_content, '</table>' ) === false ) {
						$mail_content = nl2br( $mail_content );
					}

					if ( $options['html_template'] === 'enable' ) {
						// Header mail template.
						ob_start();
						include AWSM_Job_Openings::get_template_path( 'header.php', 'mail' );
						$header_template  = ob_get_clean();
						$header_template .= '<div style="padding: 0 15px; font-size: 16px; max-width: 512px; margin: 0 auto;">';

						// Footer mail template.
						ob_start();
						include AWSM_Job_Openings::get_template_path( 'footer.php', 'mail' );
						$footer_template = ob_get_clean();
						$footer_template = '</div>' . $footer_template;

						$template = $header_template . $mail_content . $footer_template;
						/**
						 * Filters the applicant or admin notification mail template.
						 *
						 * @since 2.0.0
						 *
						 * @param string $template Mail template.
						 * @param array $template_data Mail template data.
						 * @param array $applicant_details Applicant details.
						 */
						$mail_content = apply_filters(
							"awsm_jobs_{$type}_notification_mail_template",
							$template,
							array(
								'header' => $header_template,
								'main'   => $mail_content,
								'footer' => $footer_template,
							),
							$applicant_details
						);
					} else {
						// Basic mail template.
						ob_start();
						include AWSM_Job_Openings::get_template_path( 'basic.php', 'mail' );
						$basic_template = ob_get_clean();
						$mail_content   = str_replace( '{mail-content}', $mail_content, $basic_template );
					}

					$tag_names[]  = '{mail-subject}';
					$tag_values[] = $subject;
					$mail_content = str_replace( $tag_names, $tag_values, $mail_content );

					$to = $applicant_email;
					if ( $type !== 'applicant' ) {
						$to = str_replace( $email_tag_names, $email_tag_values, $options['to'] );
					}
					AWSM_Job_Openings::log(
						array(
							'to'           => $to,
							'subject'      => $subject,
							'mail_content' => $mail_content,
							'headers'      => $headers,
							'attachments'  => $attachments,
						)
					);

					add_filter( 'wp_mail_content_type', 'awsm_jobs_mail_content_type' );
					// Now, send the mail.
					$is_mail_send = wp_mail( $to, $subject, $mail_content, array_values( $headers ), $attachments );
					remove_filter( 'wp_mail_content_type', 'awsm_jobs_mail_content_type' );

					if ( $is_mail_send ) {
						if ( $type === 'applicant' ) {
							// Save the applicant notification details.
							$current_time = current_time( 'mysql' );
							$mails_data   = array(
								array(
									'send_by'      => 0,
									'mail_date'    => $current_time,
									'cc'           => $cc,
									'subject'      => $subject,
									'mail_content' => str_replace( $tag_names, $tag_values, nl2br( $options['content'] ) ),
								),
							);
							update_post_meta( $applicant_details['application_id'], 'awsm_application_mails', $mails_data );
						} elseif ( $type === 'admin' ) {
							if ( ! empty( $admin_attachments ) ) {
								foreach ( $admin_attachments as $admin_attachment ) {
									if ( isset( $admin_attachment['temp'] ) && $admin_attachment['temp'] === true ) {
										unlink( $admin_attachment['file'] );
									}
								}
							}
						}

						/**
						 * Fires when applicant or admin notification mail is successfully sent.
						 *
						 * @since 1.4
						 *
						 * @param array $applicant_details Applicant details.
						 */
						do_action( "awsm_job_{$type}_mail_sent", $applicant_details );
					} else {
						/**
						 * Fires when applicant or admin notification mail is failed to send.
						 *
						 * @since 1.4
						 *
						 * @param array $applicant_details Applicant details.
						 */
						do_action( "awsm_job_{$type}_mail_failed", $applicant_details );
					}
				}
			}
		}
	}

	/**
	 * Get captcha frontend configuration (render + script)
	 *
	 * @return array Captcha frontend configuration array
	 */
	public static function get_captcha_frontend_config() {
		$config = array(
			'recaptcha' => array(
				'token_field'   => 'g-recaptcha-response',
				'verify_url'    => 'https://www.google.com/recaptcha/api/siteverify',
				'requires_ip'   => true,
				'requires_keys' => true,
				'render'        => array(
					'class'      => 'g-recaptcha',
					'data_attrs' => array(),
					'noscript'   => 'render_recaptcha_noscript',
				),
				'script'        => array(
					'handle'    => 'awsm-jobs-g-recaptcha',
					'src'       => 'https://www.google.com/recaptcha/api.js',
					'version'   => '2.0',
					'in_footer' => false,
					'strategy'  => 'defer',
					'async'     => true,
				),
				'conflict'      => array(
					'handles' => array( 'recaptcha', 'google-recaptcha', 'g-recaptcha', 'grecaptcha', 'google_recaptcha' ),
					'urls'    => array( 'google.com/recaptcha', 'www.google.com/recaptcha', 'gstatic.com/recaptcha' ),
				),
			),
			'hcaptcha'  => array(
				'token_field'   => 'h-captcha-response',
				'verify_url'    => 'https://hcaptcha.com/siteverify',
				'requires_ip'   => true,
				'requires_keys' => true,
				'render'        => array(
					'class'      => 'h-captcha',
					'data_attrs' => array(),
					'noscript'   => null,
				),
				'script'        => array(
					'handle'    => 'awsm-jobs-h-captcha',
					'src'       => 'https://js.hcaptcha.com/1/api.js',
					'version'   => null,
					'in_footer' => false,
					'strategy'  => 'defer',
					'async'     => true,
				),
				'conflict'      => array(
					'handles' => array( 'hcaptcha', 'h-captcha', 'hcaptcha-api', 'hcaptcha_api' ),
					'urls'    => array( 'hcaptcha.com', 'js.hcaptcha.com' ),
				),
			),
			'turnstile' => array(
				'token_field'   => 'cf-turnstile-response',
				'verify_url'    => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
				'requires_ip'   => false,
				'requires_keys' => true,
				'render'        => array(
					'class'      => 'cf-turnstile',
					'data_attrs' => array(),
					'noscript'   => null,
				),
				'script'        => array(
					'handle'    => 'awsm-jobs-cf-turnstile',
					'src'       => 'https://challenges.cloudflare.com/turnstile/v0/api.js',
					'version'   => null,

					'in_footer' => false,
					'strategy'  => 'defer',
					'async'     => true,
				),
				'conflict'      => array(
					'handles' => array( 'turnstile', 'cf-turnstile', 'cloudflare-turnstile', 'cloudflare_turnstile' ),
					'urls'    => array( 'challenges.cloudflare.com/turnstile' ),
				),
			),
			'none'      => array(
				'token_field'   => null,
				'verify_url'    => null,
				'requires_ip'   => false,
				'requires_keys' => false,
				'render'        => null,
				'script'        => null,
				'conflict'      => null,
			),
		);

		return apply_filters( 'awsm_jobs_captcha_frontend_config', $config );
	}
	public function get_captcha_type() {
		return get_option( 'awsm_jobs_enable_captcha', 'none' );
	}


	private function get_captcha_site_key( $captcha_type ) {
		if ( empty( $captcha_type ) || $captcha_type === 'none' ) {
			return null;
		}

		// If recaptcha type is v3
		if ( get_option( 'awsm_jobs_recaptcha_type' ) === 'v3' && $captcha_type === 'recaptcha' ) {
			return get_option( 'awsm_jobs_recaptcha_v3_site_key' );
		}

		// Default
		return get_option( 'awsm_jobs_' . $captcha_type . '_site_key' );
	}

	private function get_captcha_secret_key( $captcha_type ) {
		if ( empty( $captcha_type ) || $captcha_type === 'none' ) {
			return null;
		}

		// If recaptcha type is v3
		if ( get_option( 'awsm_jobs_recaptcha_type' ) === 'v3' && $captcha_type === 'recaptcha' ) {
			return get_option( 'awsm_jobs_recaptcha_v3_secret_key' );
		}

		// Default
		return get_option( 'awsm_jobs_' . $captcha_type . '_secret_key' );
	}


	public function is_captcha_set() {
		$captcha_type = $this->get_captcha_type();
		if ( empty( $captcha_type ) || 'none' === $captcha_type ) {
			return false;
		}

		$config = $this::get_captcha_frontend_config();

		if ( ! isset( $config[ $captcha_type ] ) ) {
			return false;
		}

		if ( empty( $config[ $captcha_type ]['requires_keys'] ) || ! $config[ $captcha_type ]['requires_keys'] ) {
			return false;
		}

		$site_key   = $this->get_captcha_site_key( $captcha_type );
		$secret_key = $this->get_captcha_secret_key( $captcha_type );

		if ( empty( $site_key ) || empty( $secret_key ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Enqueue CAPTCHA scripts
	 */

	public function enqueue_captcha_scripts() {
		if ( ! $this->is_captcha_set() ) {
			return;
		}

		if ( $this->is_no_conflict_mode_enabled() ) {
			$this->dequeue_conflicting_captcha_scripts();
		}

		$captcha_type = $this->get_captcha_type();
		$config       = $this::get_captcha_frontend_config();

		if ( ! isset( $config[ $captcha_type ] ) || empty( $config[ $captcha_type ]['script'] ) ) {
			return;
		}

		$script = $config[ $captcha_type ]['script'];

		if ( 'recaptcha' === $captcha_type ) {
			$recaptcha_type  = $this->get_recaptcha_type();
			$option          = get_option( 'awsm_jobs_recaptcha_fail_message' );
			$recaptcha_error = ! empty( $option )
				? $option
				: esc_html__( 'reCAPTCHA verification failed. Please try again.', 'wp-job-openings' );

			if ( 'v3' === $recaptcha_type || 'v2_invisible' === $recaptcha_type ) {
				$site_key = $this->get_captcha_site_key( $captcha_type );

				if ( ! empty( $site_key ) ) {
					$recaptcha_api_url = "https://www.google.com/recaptcha/api.js?render={$site_key}";

					wp_dequeue_script( 'awsm-jobs-g-recaptcha' );
					wp_deregister_script( 'awsm-jobs-g-recaptcha' );

					wp_enqueue_script(
						'awsm-jobs-g-recaptcha',
						esc_url( $recaptcha_api_url ),
						array(),
						'3.0',
						array(
							'in_footer' => false,
							'strategy'  => 'defer',
						)
					);

					$inline_script = sprintf(
						'var awsmJobsRecaptcha = %s;',
						wp_json_encode(
							array(
								'site_key'  => $site_key,
								'action'    => 'applicationform',
								'type'      => $recaptcha_type,
								'error_msg' => $recaptcha_error,
							)
						)
					);
					wp_add_inline_script( 'awsm-jobs-g-recaptcha', $inline_script, 'after' );

					do_action( 'awsm_jobs_captcha_scripts_enqueued', $captcha_type, $script );

					return;
				}
			}
		}

		$script = apply_filters( 'awsm_jobs_captcha_script_config', $script, $captcha_type );

		wp_enqueue_script(
			$script['handle'],
			$script['src'],
			isset( $script['deps'] ) ? $script['deps'] : array(),
			$script['version'],
			array(
				'in_footer' => $script['in_footer'],
				'strategy'  => $script['strategy'],
			)
		);
		if ( ! empty( $script['async'] ) ) {
			add_filter(
				'script_loader_tag',
				function( $tag, $handle, $src ) use ( $config ) {
					$captcha_handles = array();

					foreach ( $config as $captcha_type => $captcha_config ) {
						if ( isset( $captcha_config['script']['handle'] ) && ! empty( $captcha_config['script']['async'] ) ) {
							$captcha_handles[] = $captcha_config['script']['handle'];
						}
					}

					if ( in_array( $handle, $captcha_handles, true ) && strpos( $tag, ' async' ) === false ) {
						$tag = str_replace( ' defer', ' async defer', $tag );
					}

					return $tag;
				},
				10,
				3
			);
		}

		do_action( 'awsm_jobs_captcha_scripts_enqueued', $captcha_type, $script );
	}
	/**
	 * Get CAPTCHA verification response from provider
	 *
	 * @param string $token        The response token.
	 * @param string $captcha_type CAPTCHA type. If empty, will use the configured type.
	 * @return array Decoded response array from provider
	 */
	public function get_captcha_response( $token, $captcha_type = '' ) {
		if ( empty( $captcha_type ) ) {
			$captcha_type = $this->get_captcha_type();
		}

		$config = self::get_captcha_frontend_config();

		if ( ! isset( $config[ $captcha_type ] ) ) {
			return array();
		}

		if ( empty( $config[ $captcha_type ]['verify_url'] ) ) {
			return array();
		}

		$captcha_config = $config[ $captcha_type ];
		$secret_key     = $this->get_captcha_secret_key( $captcha_type );

		if ( empty( $secret_key ) ) {
			return array();
		}

		$body = array(
			'secret'   => $secret_key,
			'response' => $token,
		);

		if ( ! empty( $captcha_config['requires_ip'] ) ) {
			$remote_ip = $this->get_client_ip();
			if ( ! empty( $remote_ip ) ) {
				$body['remoteip'] = $remote_ip;
			}
		}

		/**
		 * Filter CAPTCHA verification request body.
		 *
		 * Allows adding custom parameters for verification (e.g., custom data).
		 *
		 * @since 4.0.0
		 *
		 * @param array  $body         Request body array.
		 * @param string $captcha_type CAPTCHA type being verified.
		 * @param string $token        The response token.
		 */
		$body = apply_filters( 'awsm_jobs_captcha_verify_request_body', $body, $captcha_type, $token );

		// Make the verification request
		$response = wp_safe_remote_post(
			$captcha_config['verify_url'],
			array(
				'body'    => $body,
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			/**
			 * Fires when CAPTCHA verification request fails.
			 *
			 * @since 4.0.0
			 *
			 * @param WP_Error $response      The error object.
			 * @param string   $captcha_type  CAPTCHA type.
			 * @param string   $token         The response token.
			 */
			do_action( 'awsm_jobs_captcha_verify_request_error', $response, $captcha_type, $token );
			return array();
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		$result = array();

		if ( 200 === $response_code && ! empty( $response_body ) ) {
			$result = json_decode( $response_body, true );

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return array();
			}
		}

		/**
		 * Filter raw CAPTCHA verification response.
		 *
		 * This is the primary hook for custom CAPTCHA validation logic.
		 * Allows complete control over how responses are processed.
		 *
		 * @since 4.0.0
		 *
		 * @param array  $result       Decoded response array from provider.
		 * @param string $captcha_type CAPTCHA type being verified.
		 * @param string $token        The response token.
		 * @param array  $body         Request body sent to provider.
		 */
		return apply_filters( 'awsm_jobs_captcha_verify_response', $result, $captcha_type, $token, $body );
	}

	/**
	 * Get client IP address
	 *
	 * @return string The client IP address
	 */
	private function get_client_ip() {
		$ip_keys = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		/**
		 * Filter IP header priority order.
		 *
		 * @since 3.6.0
		 *
		 * @param array $ip_keys Array of $_SERVER keys to check for IP.
		 */
		$ip_keys = apply_filters( 'awsm_jobs_captcha_ip_headers', $ip_keys );

		foreach ( $ip_keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );

				if ( strpos( $ip, ',' ) !== false ) {
					$ips = explode( ',', $ip );
					$ip  = trim( $ips[0] );
				}

				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					return $ip;
				}
			}
		}

		$fallback_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		/**
		 * Filter the detected client IP.
		 *
		 * @since 3.6.0
		 *
		 * @param string $fallback_ip The detected IP address.
		 */
		return apply_filters( 'awsm_jobs_captcha_client_ip', $fallback_ip );
	}
	/**
	 * Display CAPTCHA field in the application form
	 *
	 * @param array $form_attrs Attributes array for the form
	 * @return void
	 */
	public function get_recaptcha_type() {
		$type = get_option( 'awsm_jobs_recaptcha_type', 'v2' );
		if ( empty( $type ) ) {
			$type = 'v2';
		}
		return $type;
	}

	public function is_recaptcha_visible( $is_visible ) {
		if ( $this->get_recaptcha_type() === 'v3' || $this->get_recaptcha_type() === 'v2_invisible' ) {
			$is_visible = false;
		}
		return $is_visible;
	}

	public function display_captcha_field( $form_attrs ) {
		if ( ! $this->is_captcha_set() ) {
			return;
		}

		$captcha_type = $this->get_captcha_type();
		$is_visible   = true;

		// Only apply visibility logic for reCAPTCHA
		if ( $captcha_type === 'recaptcha' ) {
			$is_visible = $this->is_recaptcha_visible( true );
		}

		/**
		 * Filters the CAPTCHA visibility in the application form.
		 *
		 * @since 2.2.0
		 * @since 2.2.1 The `$form_attrs` parameter was added.
		 *
		 * @param bool  $is_visible Whether the CAPTCHA is visible or not in the form.
		 * @param array $form_attrs Attributes array for the form.
		 */
		$is_visible = apply_filters( 'awsm_application_form_is_recaptcha_visible', $is_visible, $form_attrs );

		if ( ! $is_visible ) {
			return;
		}

		$site_key = $this->get_captcha_site_key( $captcha_type );

		if ( empty( $site_key ) ) {
			return;
		}

		if ( $captcha_type === 'recaptcha' &&
			$this->get_recaptcha_type() === 'v2_invisible'
		) {
			return;
		}

		?>
		<div class="awsm-job-form-group awsm-job-captcha-group awsm-job-<?php echo esc_attr( $captcha_type ); ?>-group">
			<?php $this->render_captcha( $captcha_type, $site_key ); ?>
		</div>
		<?php
	}

	private function render_captcha( $captcha_type, $site_key ) {
		/**
		 * Allows custom rendering for captcha types.
		 *
		 * If a custom renderer returns true, the default rendering will be skipped.
		 *
		 * @since 3.6.0
		 *
		 * @param bool   $rendered     Whether custom rendering was performed.
		 * @param string $captcha_type The type of captcha being rendered.
		 * @param string $site_key     The site key for the captcha.
		 */
		$custom_rendered = apply_filters( 'awsm_jobs_render_captcha', false, $captcha_type, $site_key );

		if ( $custom_rendered ) {
			return;
		}

		$config = $this::get_captcha_frontend_config();

		if ( ! isset( $config[ $captcha_type ] ) || empty( $config[ $captcha_type ]['render'] ) ) {
			return;
		}

		$render_config = $config[ $captcha_type ]['render'];

		echo '<div class="' . esc_attr( $render_config['class'] ) . '" data-sitekey="' . esc_attr( $site_key ) . '"';

		if ( ! empty( $render_config['data_attrs'] ) ) {
			foreach ( $render_config['data_attrs'] as $attr => $value ) {
				echo ' data-' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		echo '></div>';

		if ( ! empty( $render_config['noscript'] ) ) {
			$noscript_method = $render_config['noscript'];
			if ( method_exists( $this, $noscript_method ) ) {
				$this->{$noscript_method}( $site_key );
			}
		}
	}
	/**
	 * Render reCAPTCHA noscript fallback
	 *
	 * @param string $site_key The site key for reCAPTCHA
	 * @return void
	 */
	private function render_recaptcha_noscript( $site_key ) {
		$fallback_url = add_query_arg( 'k', $site_key, 'https://www.google.com/recaptcha/api/fallback' );
		?>
		<noscript>
			<div style="width: 302px; height: 422px; position: relative;">
				<div style="width: 302px; height: 422px; position: absolute;">
					<iframe src="<?php echo esc_url( $fallback_url ); ?>" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
				</div>
				<div style="width: 300px; height: 60px; border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
					<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;"></textarea>
				</div>
			</div>
		</noscript>
		<?php
	}

	/**
	 * Get CAPTCHA response token from POST data
	 *
	 * @return string The CAPTCHA response token
	 */
	public function get_captcha_token_from_post() {
		$captcha_type = $this->get_captcha_type();
		$config       = self::get_captcha_frontend_config();

		$token_field = isset( $config[ $captcha_type ]['token_field'] )
			? $config[ $captcha_type ]['token_field']
			: '';

		$token = '';
		if ( $token_field && isset( $_POST[ $token_field ] ) ) { // phpcs:disable WordPress.Security.NonceVerification.Missing
			$token = sanitize_text_field( wp_unslash( $_POST[ $token_field ] ) );
		}
		return $token;
	}

	/**
	 * Validate CAPTCHA in application form submission
	 *
	 * @param array $awsm_response Response array to add errors to
	 * @return array Modified response array
	 */
	public function validate_captcha_submission( $awsm_response ) {
		if ( ! $this->is_captcha_set() ) {
			return $awsm_response;
		}

		$is_human     = false;
		$captcha_type = $this->get_captcha_type();
		$token        = $this->get_captcha_token_from_post();
		$result       = array();
		if ( ! empty( $token ) ) {
			$result   = $this->get_captcha_response( $token, $captcha_type );
			$is_human = false;

			if ( ! empty( $result ) && ! empty( $result['success'] ) ) {
				if ( $captcha_type === 'recaptcha' && $this->get_recaptcha_type() === 'v3' ) {
					$score_threshold = 0.5;
					$score           = isset( $result['score'] ) ? (float) $result['score'] : 0;
					$expected_action = 'applicationform';
					$action_valid    = ! empty( $result['action'] ) && $result['action'] === $expected_action;
					if ( $score >= $score_threshold && $action_valid ) {
						$is_human = true;
					}
				} else {
					$is_human = true;
				}
			}
		}

		if ( ! $is_human ) {
			$option_name   = "awsm_jobs_{$captcha_type}_fail_message";
			$saved_message = get_option( $option_name, '' );

			$default_message = __( 'CAPTCHA verification failed. Please try again.', 'wp-job-openings' );

			$message = ! empty( $saved_message ) ? $saved_message : $default_message;

			$error_codes = isset( $result['error-codes'] ) ? (array) $result['error-codes'] : array();

			/**
			 * Allow final customization of the fail message shown to applicants.
			 *
			 * @param string $message      The message that will be displayed.
			 * @param string $captcha_type Current provider key (recaptcha|hcaptcha|turnstile|...).
			 * @param array  $error_codes  Provider-specific error codes (if any).
			 * @param array  $result       Raw verification response array.
			 */
			$message = apply_filters( 'awsm_jobs_captcha_fail_message', $message, $captcha_type, $error_codes, $result );

			$awsm_response['error'][] = wp_kses_post( $message );

			/**
			 * Action: fired when CAPTCHA verification fails.
			 *
			 * @param string $captcha_type
			 * @param array  $error_codes
			 * @param string $token
			 * @param array  $result
			 */
			do_action( 'awsm_jobs_captcha_failed', $captcha_type, $error_codes, $token, $result );
		}

		return $awsm_response;
	}

	/**
	 * Check if no-conflict mode is enabled
	 *
	 * @return bool True if no-conflict mode is enabled
	 */
	private function is_no_conflict_mode_enabled() {
		$no_conflict = get_option( 'awsm_jobs_captcha_no_conflict_scripts', '' );
		return 'on' === $no_conflict;
	}

	public function dequeue_conflicting_captcha_scripts() {
		$current_type = $this->get_captcha_type();
		if ( empty( $current_type ) || $current_type === 'none' ) {
			return;
		}

		$config = self::get_captcha_frontend_config();
		if ( empty( $config ) || ! is_array( $config ) ) {
			return;
		}

		$conflict_urls = array();
		foreach ( $config as $type => $provider ) {
			if ( $type === 'none' || empty( $provider['conflict'] ) ) {
				continue;
			}
			if ( $type === $current_type ) {
				continue;
			}
			if ( ! empty( $provider['conflict']['urls'] ) && is_array( $provider['conflict']['urls'] ) ) {
				$conflict_urls = array_merge( $conflict_urls, $provider['conflict']['urls'] );
			}
		}
		$conflict_urls = array_values( array_unique( $conflict_urls ) );

		$own_handle = ! empty( $config[ $current_type ]['script']['handle'] )
			? (string) $config[ $current_type ]['script']['handle']
			: null;

		$own_prefix = 'awsm-jobs';

		$scripts = wp_scripts();
		if ( empty( $scripts ) || empty( $scripts->queue ) ) {
			return;
		}

		$dequeued = array();

		foreach ( $scripts->queue as $handle ) {
			if ( ! isset( $scripts->registered[ $handle ] ) ) {
				continue;
			}

			$reg = $scripts->registered[ $handle ];

			if ( $own_handle && $handle === $own_handle ) {
				continue;
			}

			if ( false !== strpos( $reg->handle, $own_prefix ) ) {
				continue;
			}

			$src = isset( $reg->src ) ? (string) $reg->src : '';
			if ( $src === '' ) {
				continue;
			}

			foreach ( $conflict_urls as $url ) {
				if ( $url !== '' && strpos( $src, $url ) !== false ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
					$dequeued[] = $handle;
					break;
				}
			}
		}

		do_action( 'awsm_jobs_dequeued_conflicting_scripts', $dequeued );
	}

	/**
	 * Initialize no-conflict mode hooks (late priority like WPForms).
	 *
	 * @return void
	 */
	public function init_no_conflict_mode() {
		if ( ! $this->is_no_conflict_mode_enabled() ) {
			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_conflicting_captcha_scripts' ), 9999 );
		add_action( 'wp_print_scripts', array( $this, 'dequeue_conflicting_captcha_scripts' ), 9999 );
	}
}
