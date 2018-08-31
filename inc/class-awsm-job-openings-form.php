<?php
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWSM_Job_Openings_Form {
    private static $_instance = null;

    public function __construct( ) {
        add_action( 'awsm_application_form_init', array( $this, 'application_form' ) );
        add_action( 'awsm_application_form_field_init', array( $this, 'form_field_init' ) );
        add_action( 'before_awsm_job_details', array( $this, 'insert_application' ) );
        add_action( 'wp_ajax_awsm_applicant_form_submission', array( $this, 'ajax_handle' ) );
        add_action( 'wp_ajax_nopriv_awsm_applicant_form_submission', array( $this, 'ajax_handle' ) );
    }

    public static function init() {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function form_fields() {
        $gdpr_cb_text = get_option( 'awsm_gdpr_cb_text' );
        $gdpr_enable = get_option( 'awsm_enable_gdpr_cb' );
        $allowed_file_types = get_option( 'awsm_jobs_admin_upload_file_ext' );
        $allowed_file_content = '';
        if( is_array( $allowed_file_types ) && ! empty( $allowed_file_types ) ) {
            $allowed_file_types = '.' . join( ', .', $allowed_file_types );
            $allowed_file_content = '<small>' . sprintf( esc_html__( 'Allowed Type(s): %1$s', 'wp-job-openings' ), $allowed_file_types ) . '</small>';
        }

        $default_form_fields = array(
            'awsm_applicant_name' => array(
                'label'        => __( 'Full Name', 'wp-job-openings' ),
                'id'           => 'awsm-applicant-name',
                'class'        => array( 'awsm-job-form-control' )
            ),

            'awsm_applicant_email' => array(
                'label'        => __( 'Email', 'wp-job-openings' ),
                'field_type'   => array(
                    'tag'  => 'input',
                    'type' => 'email'
                ),
                'id'           => 'awsm-applicant-email',
                'class'        => array( 'awsm-job-form-control' ),
                'error'        => array(
                    'error_rule' => 'email',
                    'error_msg'  => __( 'Please enter a valid email address.', 'wp-job-openings' )
                )
            ),

            'awsm_applicant_phone' => array(
                'label'        => __( 'Phone', 'wp-job-openings' ),
                'field_type'   => array(
                    'tag'  => 'input',
                    'type' => 'tel'
                ),
                'id'           => 'awsm-applicant-phone',
                'class'        => array( 'awsm-job-form-control' ),
                'error'        => array(
                    'error_msg'  => __( 'Please enter a valid phone number.', 'wp-job-openings' )
                )
            ),

            'awsm_applicant_letter' => array(
                'label'        => __( 'Cover Letter', 'wp-job-openings' ),
                'field_type'   => array(
                    'tag'  => 'textarea'
                ),
                'id'           => 'awsm-cover-letter',
                'class'        => array( 'awsm-job-form-control' )
            ),

            'awsm_file' => array(
                'label'        => __( 'Upload CV/Resume', 'wp-job-openings' ),
                'field_type'   => array(
                    'tag'    => 'input',
                    'type'   => 'file',
                    'accept' => $allowed_file_types
                ),
                'id'           => 'awsm-application-file',
                'class'        => array( 'awsm-resume-file-control', 'awsm-job-form-control' ),
                'content'      => $allowed_file_content
            ),

            'awsm_form_privacy_policy' => array(
                'show_field'   => ( ! empty( $gdpr_cb_text ) && ! empty( $gdpr_enable ) ),
                'label'        => html_entity_decode( $gdpr_cb_text, ENT_QUOTES ),
                'field_type'   => array(
                    'tag'   => 'input',
                    'type'  => 'checkbox',
                    'value' => 'yes'
                ),
                'label_inline' => 'right'
            )
        );
        $form_fields = apply_filters( 'awsm_application_form_fields', $default_form_fields );
        return $form_fields;
    }

    public function display_fields() {
        $form_fields = $this->form_fields();
        if( ! empty( $form_fields ) ) {
            $allowed_html = array(
                'a' => array(
                    'href' => array(),
                    'title' => array()
                ),
                'br' => array(),
                'em' => array(),
                'span' => array(),
                'strong' => array(),
                'small' => array()
            );
            $required_msg = esc_html__( 'This field is required.', 'wp-job-openings' );
            $form_output = '';
            foreach( $form_fields as $field_name => $field_args ) {
                $show_field = ( isset( $field_args['show_field'] ) ) ? $field_args['show_field'] : true;
                if( $show_field ) {
                    $label = ( isset( $field_args['label'] ) ) ? $field_args['label'] : '';
                    $tag = ( isset( $field_args['field_type'] ) ) ? ( ( isset( $field_args['field_type']['tag'] ) ) ? $field_args['field_type']['tag'] : 'input' ): 'input';
                    $input_type = ( isset( $field_args['field_type'] ) ) ? ( ( isset( $field_args['field_type']['type'] ) ) ? $field_args['field_type']['type'] : 'text' ): 'text';
                    $field_id = ( isset( $field_args['id'] ) ) ? $field_args['id'] : $field_name;
                    $field_class = 'awsm-job-form-field';
                    $field_class = ( isset( $field_args['class'] ) && is_array( $field_args['class'] ) ) ? $field_class . ' ' . join( " ", $field_args['class'] ) : $field_class;
                    $required = ( isset( $field_args['required'] ) ) ? $field_args['required'] : true;
                    $required_attr = ( $required ) ? ' required' : '';
                    $data_required = ( $required ) ? ' data-msg-required="' . $required_msg . '"' : '';
                    $required_label = ( $required ) ? ' <span class="awsm-job-form-error">*</span>' : '';
                    $form_group_class = 'awsm-job-form-group';
                    $form_group_class = ( isset( $field_args['label_inline'] ) ) ? $form_group_class . ' awsm-job-inline-group' : $form_group_class;
                    $extra_content = ( isset( $field_args['content'] ) ) ? $field_args['content'] : '';
                    // Validation
                    $data_error_msg = ( isset( $field_args['error'] ) && ! empty( $field_args['error'] ) ) ? ( ( isset( $field_args['error']['error_msg'], $field_args['error']['error_rule'] ) && ! empty( $field_args['error']['error_msg'] ) && ! empty( $field_args['error']['error_rule'] ) ) ? ( ' data-rule-' . $field_args['error']['error_rule'] . '="true" data-msg-' . $field_args['error']['error_rule'] . '="' . esc_attr( $field_args['error']['error_msg'] ) . '"' ) : '' ) : '';
                    // Common attributes for tags
                    $common_attrs = sprintf( 'name="%1$s" class="%2$s" id="%3$s"%4$s', esc_attr( $field_name ), esc_attr( $field_class ), esc_attr( $field_id ), $required_attr . $data_required . $data_error_msg );
                    if( $input_type === 'file' ) {
                        $common_attrs .= isset( $field_args['field_type']['accept'] ) ? sprintf( ' accept="%s"', esc_attr( $field_args['field_type']['accept'] ) ) : '';
                    } else {
                        if( $tag !== 'textarea' ) {
                            $common_attrs .= isset( $field_args['field_type']['value'] ) ? sprintf( ' value="%s"', esc_attr( $field_args['field_type']['value'] ) ) : '';
                        }
                    }

                    $form_content = $label_content = '';
                    if( ! empty( $label ) ) {
                        $label_content = sprintf( '<label for="%2$s">%1$s</label>', wp_kses( $label, $allowed_html ) . $required_label, esc_attr( $field_id ) );
                    }
                    if( $tag === 'input' ) {
                        $form_content .= sprintf( '<input type="%1$s" %2$s />', esc_attr( $input_type ), $common_attrs );
                    } elseif( $tag === 'textarea' ) {
                        $form_content .= sprintf( '<textarea %1$s rows="5" cols="50"></textarea>', $common_attrs );
                    }
                    if( isset( $field_args['label_inline'] ) && $field_args['label_inline'] === 'right' ) {
                        $form_content .= $label_content;
                    } else {
                        $form_content = $label_content . $form_content;
                    }
                    $form_output .= sprintf( '<div class="%2$s">%1$s</div>', $form_content . wp_kses( $extra_content, $allowed_html ), esc_attr( $form_group_class ) );
                }
            }
            echo $form_output;
        }
    }

    public function application_form() {
        include_once plugin_dir_path( __FILE__ ) . 'templates/partials/application-form.php';
    }

    public function form_field_init() {
        $this->display_fields();
    }

    public function upload_dir( $param ) {
        if( isset( $_POST['action'] ) && $_POST['action'] == 'awsm_applicant_form_submission' ) {
            $subdir = '/' . AWSM_JOBS_UPLOAD_DIR_NAME;
            if( empty( $param['subdir'] ) ) {
                $param['path'] = $param['path'] . $subdir;
                $param['url'] = $param['url'] . $subdir;
                $param['subdir'] = $subdir;
            } else {
                $subdir .= $param['subdir'];
                $param['path'] = str_replace( $param['subdir'], $subdir, $param['path'] );
                $param['url'] = str_replace( $param['subdir'], $subdir, $param['url'] );
                $param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
            }
        }
        return $param;
    }

    public function hashed_file_name( $dir, $name, $ext ) {
        $file_name = hash( 'sha1', ( $name . uniqid( rand(), true ) ) ) . current_time( 'timestamp' );
        return sanitize_file_name( $file_name . $ext );
    }

    public function insert_application() {
        global $awsm_response;

        $awsm_response = array(
            'success' => array(),
            'error'   => array()
        );

        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == 'awsm_applicant_form_submission' ) {
            $job_id = intval( $_POST['awsm_job_id'] );
            $applicant_name =  sanitize_text_field( $_POST['awsm_applicant_name'] );
            $applicant_email = sanitize_email( $_POST['awsm_applicant_email'] );
            $applicant_phone = sanitize_text_field( $_POST['awsm_applicant_phone'] );
            $applicant_letter = awsm_jobs_sanitize_textarea( $_POST['awsm_applicant_letter'] );
            $attachment = $_FILES['awsm_file'];
            $agree_privacy_policy = false;
            $gdpr_enable = get_option( 'awsm_enable_gdpr_cb' );
            if( ! empty( $gdpr_enable ) ) {
                if( ! isset( $_POST['awsm_form_privacy_policy'] ) ||  $_POST['awsm_form_privacy_policy'] !== 'yes' ) {
                    $awsm_response['error'][] = esc_html__( "Please agree to our privacy policy.", "wp-job-openings" );
                } else {
                    $agree_privacy_policy = sanitize_text_field( $_POST['awsm_form_privacy_policy'] );
                }
            }
            if( get_post_type( $job_id ) !== 'awsm_job_openings' ) {
                $awsm_response['error'][] = esc_html__( "Error occurred: Invalid Job.", "wp-job-openings" );
            }
            if( get_post_status( $job_id ) === 'expired' ) {
                $awsm_response['error'][] = esc_html__( 'Sorry! This job is expired.', 'wp-job-openings' );
            }
            if( empty( $applicant_name ) ) {
                $awsm_response['error'][] = esc_html__( "Name is required.", "wp-job-openings" );
            } else {
                if(!preg_match("/^[a-zA-Z ]*$/",$applicant_name)) {
                  $awsm_response['error'][] = esc_html__( "Only letters and white spaces are allowed for name.", "wp-job-openings" );
                }
            }
            if(empty( $applicant_email ) ) {
                $awsm_response['error'][] = esc_html__( "Email is required.", "wp-job-openings" );
            } else {
                if( ! filter_var( $applicant_email, FILTER_VALIDATE_EMAIL ) ) {
                    $awsm_response['error'][] = esc_html__( "Invalid email format.", "wp-job-openings" );
                }
            }
            if( empty( $applicant_phone ) ) {
                $awsm_response['error'][] = esc_html__( "Contact number is required.", "wp-job-openings" );
            } else {
                if( ! preg_match( "%^[+]?[0-9()/ -]*$%", trim( $applicant_phone ) ) ) {
                    $awsm_response['error'][] = esc_html__( "Invalid phone number.", "wp-job-openings");
                }
            }
            if( empty( $applicant_letter ) ) {
                $awsm_response['error'][] = esc_html__( "Cover Letter cannot be empty.", "wp-job-openings" );
            }
            if( $attachment["error"] > 0 ) {
               $awsm_response['error'][] = esc_html__( "Please select your cv/resume.", "wp-job-openings" );
            }

            if ( count( $awsm_response['error'] ) === 0 ) {
                if ( ! isset( $_POST['awsm_application_nonce'] ) || ! wp_verify_nonce( $_POST['awsm_application_nonce'], 'awsm_insert_application_nonce' ) ) {
                    $awsm_response['error'][] = esc_html__( "Error while uploading: authenticate error.", "wp-job-openings" );
                } else {
                    if ( ! function_exists( 'wp_handle_upload' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    }
                    if ( ! function_exists( 'wp_crop_image' ) ) {
                        include( ABSPATH . 'wp-admin/includes/image.php' );
                    }
                    $mimes = array();
                    $allowed_mime_types = get_allowed_mime_types();
                    $alowed_types = get_option( 'awsm_jobs_admin_upload_file_ext' );
                    foreach( $alowed_types as $allowed_type ) {
                        if( isset( $allowed_mime_types[$allowed_type] ) ) {
                            $mimes[$allowed_type] = $allowed_mime_types[$allowed_type];
                        }
                    }
                    $override = array( 'test_form' => false, 'mimes' => $mimes, 'unique_filename_callback' => array( $this, 'hashed_file_name' )
                    );
                    add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
                    $movefile = wp_handle_upload( $attachment, $override );
                    remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );
                    if ( $movefile && ! isset( $movefile['error'] ) ) {
                        $post_base_data = array(
                            'post_title'     => $applicant_name,
                            'post_content'   => '',
                            'post_status'    => 'publish',
                            'comment_status' => 'closed'
                        );
                        $application_data = array_merge( $post_base_data, array(
                            'post_type'   => 'awsm_job_application',
                            'post_parent' => $job_id
                        ) );
                        $application_id = wp_insert_post( $application_data );

                        if ( ! empty( $application_id ) && ! is_wp_error( $application_id ) ) {
                            $attachment_data = array_merge( $post_base_data, array(
                                'post_mime_type' => $movefile['type'],
                                'guid'           => $movefile['url']
                            ) );
                            $attach_id = wp_insert_attachment( $attachment_data, $movefile['file'], $application_id );

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
                                    'awsm_attachment_id'    => $attach_id
                                );
                                if( ! empty( $agree_privacy_policy ) ) {
                                    $applicant_details['awsm_agree_privacy_policy'] = $agree_privacy_policy;
                                }
                                foreach( $applicant_details as $meta_key => $meta_value ) {
                                    update_post_meta( $application_id, $meta_key, $meta_value );
                                }
                                // application count
                                $this->applications_count( null, true, $job_id );
                                // Now, send notification email
                                $this->notification_email( $applicant_details );
                                $awsm_response['success'][] = esc_html__( "Your application has been submitted.", "wp-job-openings" );
                            } else {
                                $awsm_response['error'][] = esc_html__( 'Error in submitting your application. Please try again later!', 'wp-job-openings' );
                            }
                        } else {
                            $awsm_response['error'][] = esc_html__( 'Error in submitting your application. Please try again later!', 'wp-job-openings' );
                        }
                    } else {
                        $awsm_response['error'][] =  $movefile['error'];
                    }
                }
            }
            add_action( 'awsm_application_form_notices', array( $this, 'awsm_form_submit_notices' ) );
        }
        return $awsm_response;
    }

    public function ajax_handle() {
        $response = $this->insert_application();
        wp_send_json( $response );
    }

    public function awsm_form_submit_notices() {
        global $awsm_response;
        $msg_array = array();
        $class_name = 'awsm-default-message';
        $content = '';
        if( ! empty( $awsm_response['success'] ) ) {
            $msg_array = $awsm_response['success'];
            $class_name = 'awsm-success-message';
        } else {
            if( ! empty( $awsm_response['error'] ) ) {
                $msg_array = $awsm_response['error'];
                $class_name = 'awsm-error-message';
                $content .= '<p>' . esc_html__( 'The following errors have occurred:', 'wp-job-openings' ) . '</p>';
            }
        }
        foreach( $msg_array as $msg ) {
            $content .= '<li>' . $msg . '</li>';
        }
        printf( '<ul class="%1$s">%2$s</ul>', $class_name, $content );
    }

    public function applications_count( $application_id, $increment = true, $job_id = null ) {
        $job_id = ( is_null( $job_id ) && ! is_null( $application_id ) ) ? get_post_meta( $application_id, 'awsm_job_id', true ) : $job_id;
        if( ! empty( $job_id ) ) {
            $count = get_post_meta( $job_id, 'awsm_application_count', true );
            if( ! empty( $count ) ) {
                if( $increment ) {
                    $count++;
                } else {
                    $count--;
                }
            } else {
                $count = 1;
            }
            update_post_meta( $job_id, 'awsm_application_count', $count );
        }
    }

    private function notification_email( $applicant_details ) {
        $enable_acknowledgement = get_option( 'awsm_jobs_acknowledgement' );
        $enable_admin = get_option( 'awsm_jobs_enable_admin_notification' );
        if  ( $enable_acknowledgement == 'acknowledgement'  || $enable_admin == 'enable'  ) {
            $admin_email = get_option( 'admin_email' );
            $applicant_cc = get_option( 'awsm_jobs_hr_notification' );
            $notifi_subject = get_option( 'awsm_jobs_notification_subject' );
            $notifi_content = get_option( 'awsm_jobs_notification_content' );
            $company_name = get_option( 'awsm_job_company_name', '' );
            $hr_email = get_option( 'awsm_hr_email_address', '' );
            $admin_to = get_option( 'awsm_jobs_admin_to_notification' );
            $admin_cc = get_option( 'awsm_jobs_admin_hr_notification' );
            $admin_subject = get_option( 'awsm_jobs_admin_notification_subject' );
            $admin_content = get_option( 'awsm_jobs_admin_notification_content' );
            $job_expiry = get_post_meta( $applicant_details['awsm_job_id'], 'awsm_job_expiry', true);
            $job_expiry = ( ! empty( $job_expiry ) ) ? date_i18n( __( get_option( 'date_format' ) ), strtotime( $job_expiry ) ) : '';
            $attachment_url = wp_get_attachment_url( $applicant_details['awsm_attachment_id'] );
            $notification_details = array(
                '{applicant}'        => $applicant_details['awsm_applicant_name'],
                '{applicant-email}'  => $applicant_details['awsm_applicant_email'],
                '{applicant-phone}'  => $applicant_details['awsm_applicant_phone'],
                '{job-id}'           => $applicant_details['awsm_job_id'],
                '{job-expiry}'       => $job_expiry,
                '{admin-email}'      => $admin_email,
                '{hr-email}'         => $hr_email,
                '{company}'          => $company_name,
                '{job-title}'        => $applicant_details['awsm_apply_for'],
                '{applicant-cover}'  => $applicant_details['awsm_applicant_letter'],
                '{applicant-resume}' => ( ! empty( $attachment_url ) ) ? esc_url( $attachment_url ) : ''
            );
            $template_tags = array_keys( $notification_details );
            $replacement_values = array_values( $notification_details );
            if ( $enable_acknowledgement == 'acknowledgement' && ! empty( $notifi_subject ) && ! empty( $notifi_content ) ) {
                $filtered_subject = str_replace( $template_tags, $replacement_values, $notifi_subject );
                $filtered_content = str_replace( $template_tags, $replacement_values, $notifi_content );
                $to = $applicant_details['awsm_applicant_email'];
                $subject = $filtered_subject;
                $message = $filtered_content;
                $title = ( ! empty( $company_name ) ) ? $company_name : get_option( 'blogname' );
                $headers = array();
                $headers[] = 'From: ' . $title . ' <' . $admin_email . '>';
                $headers[] = 'Cc: ' . $applicant_cc;
                wp_mail( $to, $subject, $message, $headers );
            }
            if( $enable_admin == 'enable' && ! empty( $admin_subject ) && ! empty( $admin_content ) ) {
                $filtered_admin_subject = str_replace( $template_tags, $replacement_values, $admin_subject );
                $filtered_admin_content = str_replace( $template_tags, $replacement_values, $admin_content );
                $to = $admin_to;
                $subject = $filtered_admin_subject;
                $message = $filtered_admin_content;
                $admin_headers = array();
                $admin_headers[] = 'From: ' . $applicant_details['awsm_applicant_name'] . ' <' . $applicant_details['awsm_applicant_email'] . '>';
                $admin_headers[] = 'Cc: ' . $admin_cc;
                wp_mail( $to, $subject, $message, $admin_headers );
            }
        }
    }
}