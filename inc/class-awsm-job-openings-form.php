<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Form {
	private static $instance    = null;
	public $form_fields_order   = array( 'awsm_applicant_name', 'awsm_applicant_email', 'awsm_applicant_phone', 'awsm_applicant_letter', 'awsm_file' );
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
		add_action( 'before_awsm_job_details', array( $this, 'insert_application' ) );
		add_action( 'wp_ajax_awsm_applicant_form_submission', array( $this, 'ajax_handle' ) );
		add_action( 'wp_ajax_nopriv_awsm_applicant_form_submission', array( $this, 'ajax_handle' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function dynamic_form_fields() {
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
		$form_fields = apply_filters( 'awsm_application_form_fields', $default_form_fields );
		return $form_fields;
	}

	public function display_dynamic_fields() {
		$dynamic_form_fields = $this->dynamic_form_fields();
		if ( ! empty( $dynamic_form_fields ) ) {
			$ordered_form_fields = array();
			$form_fields_order   = apply_filters( 'awsm_application_form_fields_order', $this->form_fields_order );
			foreach ( $form_fields_order as $form_field_order ) {
				$ordered_form_fields[ $form_field_order ] = $dynamic_form_fields[ $form_field_order ];
			}
			$dynamic_form_fields = $ordered_form_fields;
			$allowed_html        = self::$allowed_html;
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
					 *
					 * @param string $field_content The content.
					 * @param array $field_args Form field options.
					 */
					$field_content = apply_filters( "awsm_application_dynamic_form_{$field_type}_field_content", $field_content, $field_args );
					$field_output  = sprintf( '<div class="%2$s">%1$s</div>', $field_content . wp_kses( $extra_content, $allowed_html ), esc_attr( $form_group_class ) );
					/**
					 * Filters the form field content of the job application form.
					 *
					 * @since 2.0.0
					 *
					 * @param string $field_output The content.
					 * @param string $field_type The field type.
					 * @param array $field_args Form field options.
					 */
					$form_output .= apply_filters( 'awsm_application_dynamic_form_field_content', $field_output, $field_type, $field_args );
				}
			}
			/**
			 * Filters the dynamic form fields content of the job application form.
			 *
			 * @since 1.3
			 *
			 * @param string $form_output The content.
			 * @param array $dynamic_form_fields Dynamic form fields.
			 */
			echo apply_filters( 'awsm_application_dynamic_form_fields_content', $form_output, $dynamic_form_fields ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function get_gdpr_field_label() {
		$content      = false;
		$gdpr_cb_text = get_option( 'awsm_gdpr_cb_text' );
		$gdpr_enable  = get_option( 'awsm_enable_gdpr_cb' );
		if ( ! empty( $gdpr_enable ) && ! empty( $gdpr_cb_text ) ) {
			$content = html_entity_decode( $gdpr_cb_text, ENT_QUOTES );
		}
		return $content;
	}

	public function display_gdpr_field() {
		$label = $this->get_gdpr_field_label();
		if ( ! empty( $label ) ) :
			?>
			<div class="awsm-job-form-group awsm-job-inline-group">
				<input name="awsm_form_privacy_policy" class="awsm-job-form-field" id="awsm_form_privacy_policy" required="" data-msg-required="<?php echo esc_attr__( 'This field is required.', 'wp-job-openings' ); ?>" value="yes" aria-required="true" type="checkbox"><label for="awsm_form_privacy_policy"><?php echo wp_kses( $label, self::$allowed_html ); ?> <span class="awsm-job-form-error">*</span></label>
			</div>
			<?php
		endif;
	}

	public function display_recaptcha_field() {
		if ( $this->is_recaptcha_set() ) :
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
	}

	public function application_form() {
		include AWSM_Job_Openings::get_template_path( 'form.php', 'single-job' );
	}

	public function form_field_init() {
		$this->display_dynamic_fields();
		$this->display_gdpr_field();
		$this->display_recaptcha_field();
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

	public function hashed_file_name( $dir, $name, $ext ) {
		$file_name = hash( 'sha1', ( $name . uniqid( (string) rand(), true ) ) ) . time();
		return sanitize_file_name( $file_name . $ext );
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
			$applicant_name       = sanitize_text_field( $_POST['awsm_applicant_name'] );
			$applicant_email      = sanitize_email( $_POST['awsm_applicant_email'] );
			$applicant_phone      = sanitize_text_field( $_POST['awsm_applicant_phone'] );
			$applicant_letter     = awsm_jobs_sanitize_textarea( $_POST['awsm_applicant_letter'] );
			$attachment           = $_FILES['awsm_file'];
			$agree_privacy_policy = false;
			$generic_err_msg      = esc_html__( 'Error in submitting your application. Please refresh the page and retry.', 'wp-job-openings' );
			if ( $this->is_recaptcha_set() ) {
				$is_human = false;
				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$is_human = $this->validate_captcha_field( $_POST['g-recaptcha-response'] );
				}
				if ( ! $is_human ) {
					$awsm_response['error'][] = esc_html__( 'Please verify that you are not a robot.', 'wp-job-openings' );
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
			if ( $attachment['error'] > 0 ) {
				$awsm_response['error'][] = esc_html__( 'Please select your cv/resume.', 'wp-job-openings' );
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
					$application_id   = wp_insert_post( $application_data );

					if ( ! empty( $application_id ) && ! is_wp_error( $application_id ) ) {
						$attachment_data = array_merge(
							$post_base_data,
							array(
								'post_mime_type' => $movefile['type'],
								'guid'           => $movefile['url'],
							)
						);
						$attach_id       = wp_insert_attachment( $attachment_data, $movefile['file'], $application_id );

						if ( ! empty( $attach_id ) && ! is_wp_error( $attach_id ) ) {
							$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
							wp_update_attachment_metadata( $attach_id, $attach_data );
							$applicant_details = array(
								'awsm_job_id'           => $job_id,
								'awsm_apply_for'        => esc_html( get_the_title( $job_id ) ),
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
							$awsm_response['error'][] = $generic_err_msg;
						}
					} else {
						$awsm_response['error'][] = $generic_err_msg;
					}
				} else {
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

	public function validate_captcha_field( $token ) {
		$is_valid   = false;
		$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
		if ( ! empty( $token ) ) {
			$secret_key = get_option( 'awsm_jobs_recaptcha_secret_key' );
			$response   = wp_safe_remote_post(
				$verify_url,
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
						$response = json_decode( $response_body, true );
						$is_valid = isset( $response['success'] ) && $response['success'] === true;
					}
				}
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
		$tags = array(
			'{applicant}'        => $applicant_details['awsm_applicant_name'],
			'{application-id}'   => $applicant_details['application_id'],
			'{applicant-email}'  => $applicant_details['awsm_applicant_email'],
			'{applicant-phone}'  => isset( $applicant_details['awsm_applicant_phone'] ) ? $applicant_details['awsm_applicant_phone'] : '',
			'{job-id}'           => $applicant_details['awsm_job_id'],
			'{job-expiry}'       => $job_expiry,
			'{job-title}'        => $applicant_details['awsm_apply_for'],
			'{applicant-cover}'  => isset( $applicant_details['awsm_applicant_letter'] ) ? $applicant_details['awsm_applicant_letter'] : '',
			'{applicant-resume}' => ( ! empty( $attachment_url ) ) ? esc_url( $attachment_url ) : '',
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

	protected function notification_email( $applicant_details ) {
		$enable_acknowledgement = get_option( 'awsm_jobs_acknowledgement' );
		$enable_admin           = get_option( 'awsm_jobs_enable_admin_notification' );
		if ( $enable_acknowledgement === 'acknowledgement' || $enable_admin === 'enable' ) {
			$admin_email             = get_option( 'admin_email' );
			$hr_mail                 = get_option( 'awsm_hr_email_address', '' );
			$company_name            = get_option( 'awsm_job_company_name', '' );
			$applicant_cc            = get_option( 'awsm_jobs_hr_notification', $hr_mail );
			$notifi_subject          = get_option( 'awsm_jobs_notification_subject' );
			$notifi_content          = get_option( 'awsm_jobs_notification_content' );
			$admin_to                = get_option( 'awsm_jobs_admin_to_notification', $admin_email );
			$admin_cc                = get_option( 'awsm_jobs_admin_hr_notification' );
			$admin_subject           = get_option( 'awsm_jobs_admin_notification_subject' );
			$admin_content           = get_option( 'awsm_jobs_admin_notification_content' );
			$applicant_email         = $applicant_details['awsm_applicant_email'];
			$from                    = ( ! empty( $company_name ) ) ? $company_name : get_option( 'blogname' );
			$from_email              = get_option( 'awsm_jobs_from_email_notification', $admin_email );
			$reply_to                = get_option( 'awsm_jobs_reply_to_notification', '' );
			$admin_reply_to          = get_option( 'awsm_jobs_admin_reply_to_notification', '{applicant-email}' );
			$admin_from_email        = get_option( 'awsm_jobs_admin_from_email_notification', $admin_email );
			$applicant_mail_template = get_option( 'awsm_jobs_notification_mail_template' );
			$admin_mail_template     = get_option( 'awsm_jobs_notification_admin_mail_template' );

			$tags             = $this->get_mail_template_tags(
				$applicant_details,
				array(
					'admin_email'  => $admin_email,
					'hr_email'     => $hr_mail,
					'company_name' => $company_name,
				)
			);
			$tag_names        = array_keys( $tags );
			$tag_values       = array_values( $tags );
			$email_tag_names  = array( '{admin-email}', '{hr-email}', '{applicant-email}' );
			$email_tag_values = array( $admin_email, $hr_mail, $applicant_email );

			// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
			$header_template = $footer_template = '';
			if ( $applicant_mail_template === 'enable' || $admin_mail_template === 'enable' ) {
				// Header mail template.
				ob_start();
				include AWSM_Job_Openings::get_template_path( 'header.php', 'mail' );
				$header_template  = ob_get_clean();
				$header_template .= '<div style="padding: 0 15px; font-size: 16px; max-width: 512px; margin: 0 auto;">';

				// Footer mail template.
				ob_start();
				include AWSM_Job_Openings::get_template_path( 'footer.php', 'mail' );
				$footer_template  = ob_get_clean();
				$footer_template .= '</div>';
			}

			if ( $enable_acknowledgement === 'acknowledgement' && ! empty( $notifi_subject ) && ! empty( $notifi_content ) ) {
				$subject      = str_replace( $tag_names, $tag_values, $notifi_subject );
				$reply_to     = str_replace( $email_tag_names, $email_tag_values, $reply_to );
				$applicant_cc = str_replace( $email_tag_names, $email_tag_values, $applicant_cc );

				/**
				 * Filters the applicant notification mail headers.
				 *
				 * @since 1.4
				 *
				 * @param array $headers Additional headers
				 */
				$headers = apply_filters(
					'awsm_jobs_applicant_notification_mail_headers',
					array(
						'content_type' => 'Content-Type: text/html; charset=UTF-8',
						'from'         => sprintf( 'From: %1$s <%2$s>', $from, $from_email ),
						'reply_to'     => 'Reply-To: ' . $reply_to,
						'cc'           => 'Cc: ' . $applicant_cc,
					),
					$applicant_details
				);

				$reply_to = trim( str_replace( 'Reply-To:', '', $headers['reply_to'] ) );
				if ( empty( $reply_to ) ) {
					unset( $headers['reply_to'] );
				}

				$cc = trim( str_replace( 'Cc:', '', $headers['cc'] ) );
				if ( empty( $cc ) ) {
					unset( $headers['cc'] );
				}

				/**
				 * Filters the applicant notification mail attachments.
				 *
				 * @since 1.4
				 *
				 * @param array $attachments Mail attachments.
				 * @param array $applicant_details Applicant details.
				 */
				$attachments = apply_filters( 'awsm_jobs_applicant_notification_mail_attachments', array(), $applicant_details );

				$mail_content = nl2br( $notifi_content );
				if ( $applicant_mail_template === 'enable' ) {
					$template = $header_template . $mail_content . $footer_template;
					/**
					 * Filters the applicant notification mail template.
					 *
					 * @since 2.0.0
					 *
					 * @param string $template Mail template.
					 * @param array $template_data Mail template data.
					 * @param array $applicant_details Applicant details.
					 */
					$mail_content = apply_filters(
						'awsm_jobs_applicant_notification_mail_template',
						$template,
						array(
							'header' => $header_template,
							'main'   => $mail_content,
							'footer' => $footer_template,
						),
						$applicant_details
					);
				}

				$mail_content = str_replace( $tag_names, $tag_values, $mail_content );

				// Now, send mail to the applicant.
				$is_mail_send = wp_mail( $applicant_email, $subject, $mail_content, array_values( $headers ), $attachments );

				if ( $is_mail_send ) {
					$current_time = current_time( 'mysql' );
					$mails_data   = array(
						array(
							'send_by'      => 0,
							'mail_date'    => $current_time,
							'cc'           => $applicant_cc,
							'subject'      => $subject,
							'mail_content' => str_replace( $tag_names, $tag_values, nl2br( $notifi_content ) ),
						),
					);
					update_post_meta( $applicant_details['application_id'], 'awsm_application_mails', $mails_data );

					/**
					 * Fires when applicant notification mail is successfully sent.
					 *
					 * @since 1.4
					 *
					 * @param array $applicant_details Applicant details.
					 */
					do_action( 'awsm_job_applicant_mail_sent', $applicant_details );
				} else {
					/**
					 * Fires when applicant notification mail is failed to send.
					 *
					 * @since 1.4
					 *
					 * @param array $applicant_details Applicant details.
					 */
					do_action( 'awsm_job_applicant_mail_failed', $applicant_details );
				}
			}

			if ( $enable_admin === 'enable' && ! empty( $admin_subject ) && ! empty( $admin_content ) ) {
				$to       = str_replace( $email_tag_names, $email_tag_values, $admin_to );
				$subject  = str_replace( $tag_names, $tag_values, $admin_subject );
				$reply_to = str_replace( $email_tag_names, $email_tag_values, $admin_reply_to );
				$admin_cc = str_replace( $email_tag_names, $email_tag_values, $admin_cc );

				/**
				 * Filters the admin notification mail headers.
				 *
				 * @since 1.4
				 *
				 * @param array $headers Additional headers
				 * @param array $applicant_details Applicant details
				 */
				$admin_headers = apply_filters(
					'awsm_jobs_admin_notification_mail_headers',
					array(
						'content_type' => 'Content-Type: text/html; charset=UTF-8',
						'from'         => sprintf( 'From: %1$s <%2$s>', $from, $admin_from_email ),
						'reply_to'     => 'Reply-To: ' . $reply_to,
						'cc'           => 'Cc: ' . $admin_cc,
					),
					$applicant_details
				);

				$reply_to = trim( str_replace( 'Reply-To:', '', $admin_headers['reply_to'] ) );
				if ( empty( $reply_to ) ) {
					unset( $admin_headers['reply_to'] );
				}

				$cc = trim( str_replace( 'Cc:', '', $admin_headers['cc'] ) );
				if ( empty( $cc ) ) {
					unset( $admin_headers['cc'] );
				}

				/**
				 * Filters the admin notification mail attachments.
				 *
				 * @since 1.4
				 *
				 * @param array $attachments_details Attachments details.
				 * @param array $applicant_details Applicant details.
				 */
				$attachments_details = apply_filters( 'awsm_jobs_admin_notification_mail_attachments', array(), $applicant_details );
				$attachments         = ! empty( $attachments_details ) ? wp_list_pluck( $attachments_details, 'file' ) : array();

				$mail_content = nl2br( $admin_content );
				if ( $admin_mail_template === 'enable' ) {
					$template = $header_template . $mail_content . $footer_template;
					/**
					 * Filters the admin notification mail template.
					 *
					 * @since 2.0.0
					 *
					 * @param string $template Mail template.
					 * @param array $template_data Mail template data.
					 * @param array $applicant_details Applicant details.
					 */
					$mail_content = apply_filters(
						'awsm_jobs_admin_notification_mail_template',
						$template,
						array(
							'header' => $header_template,
							'main'   => $mail_content,
							'footer' => $footer_template,
						),
						$applicant_details
					);
				}

				$mail_content = str_replace( $tag_names, $tag_values, $mail_content );

				// Now, send mail to the admin.
				$is_mail_send = wp_mail( $to, $subject, $mail_content, array_values( $admin_headers ), $attachments );

				if ( $is_mail_send ) {
					if ( ! empty( $attachments_details ) ) {
						foreach ( $attachments_details as $attachment_details ) {
							// Now, delete temporarily created files after mail is send.
							if ( isset( $attachment_details['temp'] ) && $attachment_details['temp'] === true ) {
								unlink( $attachment_details['file'] );
							}
						}
					}

					/**
					 * Fires when admin notification mail is successfully sent.
					 *
					 * @since 1.4
					 *
					 * @param array $applicant_details Applicant details.
					 */
					do_action( 'awsm_job_admin_mail_sent', $applicant_details );
				} else {
					/**
					 * Fires when admin notification mail is failed to send.
					 *
					 * @since 1.4
					 *
					 * @param array $applicant_details Applicant details.
					 */
					do_action( 'awsm_job_admin_mail_failed', $applicant_details );
				}
			}
		}
	}
}
