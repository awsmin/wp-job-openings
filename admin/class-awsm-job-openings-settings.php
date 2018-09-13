<?php
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWSM_Job_Openings_Settings {
    private static $_instance = null;

    public function __construct( $awsm_core ) {
        $this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );
        $this->awsm_core = $awsm_core;
        $this->set_settings_capability();

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'wp_ajax_settings_switch', array( $this, 'settings_switch_ajax' ) );

        add_action( 'update_option_awsm_select_page_listing', array( $this, 'update_awsm_page_listing' ), 10, 2 );
        add_action( 'update_option_awsm_permalink_slug', array( $this, 'update_awsm_permalink_slug' ), 10, 2 );
        add_action( 'update_option_awsm_jobs_remove_filters', array( $this, 'update_awsm_jobs_remove_filters' ), 10, 2 );
        add_action( 'update_option_awsm_jobs_make_specs_clickable', array( $this, 'update_awsm_jobs_make_specs_clickable' ), 10, 2 );
    }

    public static function init( $awsm_core ) {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new self( $awsm_core );
        }
        return self::$_instance;
    }

    public function settings_page_capability( $capability ) {
        return 'manage_awsm_jobs';
    }

    public function admin_menu() {
        add_submenu_page( 'edit.php?post_type=awsm_job_openings', __( 'Settings', 'wp-job-openings' ),__( 'Settings', 'wp-job-openings' ), 'manage_awsm_jobs', 'awsm-jobs-settings', array( $this, 'settings_page' ) );
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
            'notification'   => esc_html__( 'Notifications', 'wp-job-openings' )
        );
    }

    private function settings() {
        $settings = array(
            'general'   => array(
                array(
                    'option_name' => 'awsm_select_page_listing'
                ),
                array(
                    'option_name' => 'awsm_job_company_name'
                ),
                array(
                    'option_name' => 'awsm_hr_email_address',
                    'callback'    => 'sanitize_email'
                ),
                array(
                    'option_name' => 'awsm_permalink_slug',
                    'callback'    =>  array( $this, 'sanitize_permalink_slug' )
                ),
                array(
                    'option_name' => 'awsm_delete_data_on_uninstall'
                ),
                array(
                    'option_name' => 'awsm_default_msg'
                )
            ),
            'appearance' => array(
                array(
                    'option_name' => 'awsm_current_appearance_subtab'
                ),
                array(
                    'option_name' => 'awsm_jobs_listing_view'
                ),
                array(
                    'option_name' => 'awsm_jobs_list_per_page',
                    'callback'    => array( $this, 'sanitize_list_per_page' )
                ),
                array(
                    'option_name' => 'awsm_jobs_number_of_columns',
                    'callback'    => 'intval'
                ),
                array(
                    'option_name' => 'awsm_enable_job_filter_listing'
                ),
                array(
                    'option_name' => 'awsm_jobs_listing_available_filters',
                    'callback'    => array( $this, 'sanitize_array_fields' )
                ),
                array(
                    'option_name' => 'awsm_jobs_listing_specs',
                    'callback'    => array( $this, 'sanitize_array_fields' )
                ),
                array(
                    'option_name' => 'awsm_jobs_details_page_template' /** @since 1.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_details_page_layout'
                ),
                array(
                    'option_name' => 'awsm_jobs_expired_jobs_listings'
                ),
                array(
                    'option_name' => 'awsm_jobs_specification_job_detail' /** @since 1.0.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_show_specs_icon' /** @since 1.0.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_make_specs_clickable' /** @since 1.0.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_specs_position' /** @since 1.0.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_expired_jobs_content_details'
                ),
                array(
                    'option_name' => 'awsm_jobs_expired_jobs_block_search'
                ),
                array(
                    'option_name' => 'awsm_jobs_hide_expiry_date'
                )
            ),

            'specifications' => array(
                array(
                    'option_name' => 'awsm_jobs_filter',
                    'callback'    => array( $this, 'awsm_jobs_filter_handle' )
                ),
                array(
                    'option_name' => 'awsm_jobs_remove_filters',
                    'callback'    => ''
                )
            ),

            'form' => array(
                array(
                    'option_name' => 'awsm_current_form_subtab' /** @since 1.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_admin_upload_file_ext',
                    'callback'    => array( $this, 'sanitize_upload_file_extns' )
                ),
                array(
                    'option_name' => 'awsm_enable_gdpr_cb',
                ),
                array(
                    'option_name' => 'awsm_gdpr_cb_text',
                    'callback'    => array( $this, 'awsm_gdpr_cb_text_handle' )
                ),
                array(
                    'option_name' => 'awsm_jobs_enable_recaptcha' /** @since 1.1 */
                ),
                array(
                    'option_name' => 'awsm_jobs_recaptcha_site_key', /** @since 1.1 */
                    'callback'    => array( $this, 'sanitize_site_key' )
                ),
                array(
                    'option_name' => 'awsm_jobs_recaptcha_secret_key', /** @since 1.1 */
                    'callback'    => array( $this, 'sanitize_secret_key' )
                )
            ),

            'notification' => array(
                array(
                    'option_name' => 'awsm_jobs_applicant_notification'
                ),
                array(
                    'option_name' => 'awsm_jobs_hr_notification'
                ),
                array(
                    'option_name' => 'awsm_jobs_acknowledgement'
                ),
                array(
                    'option_name' => 'awsm_jobs_notification_subject'
                ),
                array(
                    'option_name' => 'awsm_jobs_notification_content',
                    'callback'    => 'awsm_jobs_sanitize_textarea'
                ),
                array(
                    'option_name' => 'awsm_jobs_admin_to_notification'
                ),
                array(
                    'option_name' => 'awsm_jobs_enable_admin_notification'
                ),
                array(
                    'option_name' => 'awsm_jobs_admin_hr_notification'
                ),
                array(
                    'option_name' => 'awsm_jobs_admin_notification_subject'
                ),
                array(
                    'option_name' => 'awsm_jobs_admin_notification_content',
                    'callback'    => 'awsm_jobs_sanitize_textarea'
                )
            )
        );
        return $settings;
    }

    private function default_settings() {
        $options = array(
            'awsm_permalink_slug'                => 'jobs',
            'awsm_default_msg'                   => esc_html__( 'We currently have no job openings', 'wp-job-openings' ),
            'awsm_jobs_listing_view'             => 'list-view',
            'awsm_jobs_list_per_page'            => 10,
            'awsm_jobs_number_of_columns'        => 3,
            'awsm_current_appearance_subtab'     => 'awsm-job-listing-nav-subtab',
            'awsm_jobs_details_page_layout'      => 'single',
            'awsm_jobs_filter'                   => array(
                array(
                    'taxonomy' => 'job-category',
                    'filter'   => esc_html( 'Job Category',  'wp-job-openings' )
                ),
                array(
                    'taxonomy' => 'job-type',
                    'filter'   => esc_html( 'Job Type',  'wp-job-openings' ),
                    'tags'     => array( 'Full Time', 'Part Time', 'Freelance' )
                ),
                array(
                    'taxonomy' => 'job-location',
                    'filter'   => esc_html( 'Job Location',  'wp-job-openings' )
                )
            ),
            'awsm_enable_job_filter_listing'      => 'enabled',
            'awsm_jobs_listing_available_filters' => array( 'job-category', 'job-type', 'job-location' ),
            'awsm_jobs_listing_specs'             => array( 'job-category', 'job-location' ),
            'awsm_jobs_admin_upload_file_ext'     => array( 'pdf', 'doc', 'docx' ),
            'awsm_enable_gdpr_cb'                 => 'true',
            'awsm_gdpr_cb_text'                   => esc_html__( 'By using this form you agree with the storage and handling of your data by this website.', 'wp-job-openings' ),
            'awsm_jobs_acknowledgement'           => 'acknowledgement',
            'awsm_jobs_notification_subject'      => 'Thanks for submitting your application for a job at {company}',
            'awsm_jobs_notification_content'      => "Dear {applicant},\n\nThis is to let you know that we have received your application.We appreciate your interest in {company} and the position of {job-title} for which you applied.  If you are selected for an interview, you can expect a phone call from our Human Resources staff shortly.\n\n Thank you, again, for your interest in our company. We do appreciate the time that you invested in this application.\n\nSincerely\n\nHR Manager\n{company}",
            'awsm_jobs_enable_admin_notification' => 'enable',
            'awsm_jobs_admin_notification_subject'=> 'New application received for the position {job-title} [{job-id}]',
            'awsm_jobs_admin_notification_content'=> "Job Opening: {job-title} [{job-id}]\nName: {applicant}\nEmail: {applicant-email}\nPhone: {applicant-phone}\nResume: {applicant-resume}\nCover letter: {applicant-cover}\n\nPowered by WP Job Openings Plugin"
        );
        if( ! empty( $options ) ) {
            foreach( $options as $option => $value ) {
                if( ! get_option( $option ) ) {
                    update_option( $option, $value );
                }
            }
        }
    }

    public function register_default_settings() {
        if ( get_option( 'awsm_register_default_settings' ) == 1 ) {
            return;
        }
        $this->default_settings();
        update_option( 'awsm_register_default_settings', 1 );
    }

    public function register_settings() {
        $settings = $this->settings();
        foreach( $settings as $group=>$settings_args ) {
            foreach( $settings_args as $setting_args ) {
                register_setting( 'awsm-jobs-' . $group . '-settings', $setting_args['option_name'], isset( $setting_args['callback'] ) ? $setting_args['callback'] : 'sanitize_text_field' );
            }
        }
    }

    private function set_settings_capability() {
        $settings = $this->settings();
        foreach( $settings as $group => $settings_args ) {
            add_filter( 'option_page_capability_' . 'awsm-jobs-' . $group . '-settings', array( $this, 'settings_page_capability' ), 11 );
        }

    }

    public function file_upload_extensions() {
        $extns = array( 'pdf', 'doc', 'docx', 'rtf' );
        return apply_filters( 'awsm_jobs_form_file_extensions', $extns );
    }

    public function sanitize_permalink_slug( $input ) {
        $old_value = get_option( 'awsm_permalink_slug' );
        if( empty( $input ) ) {
            add_settings_error( 'awsm_permalink_slug', 'awsm-permalink-slug', esc_html__( 'URL slug cannot be empty.', 'wp-job-openings' ) );
            $input = $old_value;
        }
        $slug = sanitize_title( $input, 'jobs' );
        $page = get_page_by_path( $slug, ARRAY_N );
        if( ! empty( $page ) && is_array( $page ) ) {
            add_settings_error( 'awsm_permalink_slug', 'awsm-permalink-slug', esc_html__( 'Slug cannot be updated. A page with same slug exists. Please choose a different URL slug.', 'wp-job-openings' ) );
            $slug = $old_value;
        }
        return $slug;
    }

    public function sanitize_site_key( $input ) {
        $old_value = get_option( 'awsm_jobs_recaptcha_site_key' );
        if( empty( $input ) ) {
            add_settings_error( 'awsm_jobs_recaptcha_site_key', 'awsm-recaptcha-site-key', esc_html__( 'Invalid site key provided.', 'wp-job-openings' ) );
            $input = $old_value;
        }
        return $input;
    }

    public function sanitize_secret_key( $input ) {
        $old_value = get_option( 'awsm_jobs_recaptcha_secret_key' );
        if( empty( $input ) ) {
            add_settings_error( 'awsm_jobs_recaptcha_secret_key', 'awsm-recaptcha-secret-key', esc_html__( 'Invalid secret key provided.', 'wp-job-openings' ) );
            $input = $old_value;
        }
        return $input;
    }

    public function sanitize_list_per_page( $input ) {
        $number_of_columns = intval( $input );
        if( $number_of_columns < 1 ) {
            add_settings_error( 'awsm_jobs_list_per_page', 'awsm-list-per-page', esc_html__( 'Listings per page must be greater than or equal to 1.', 'wp-job-openings' ) );
            return false;
        }
        return $number_of_columns;
    }

    public function sanitize_array_fields( $input ) {
        if( is_array( $input ) ) {
            $input = array_map( 'sanitize_text_field', $input );
        }
        return $input;
    }

    public function awsm_jobs_filter_handle( $input ) {
        $filters = $input;
        if( ! empty( $filters ) ) {
            foreach( $filters as $index => $filter ) {
                if( isset( $filter['filter'] ) ) {
                    $filters[$index]['filter'] = sanitize_text_field( $filter['filter'] );
                    if( ! isset( $filter['taxonomy'] ) ) {
                        $filters[$index]['taxonomy'] = sanitize_title_with_dashes( $filter['filter'] );
                    } else {
                        $current_taxonomy = sanitize_text_field( $filter['taxonomy'] );
                        $filters[$index]['taxonomy'] = $current_taxonomy;
                        if( isset( $filter['remove_tags'] ) ) {
                            if( ! empty( $filter['remove_tags'] ) ) {
                                $remove_tags = $filter['remove_tags'];
                                if( isset( $filter['tags'] ) ) {
                                    if( ! empty( $filter['tags'] ) ) {
                                        $remove_tags = array_diff( $remove_tags, $filter['tags'] );
                                    }
                                }
                                if( ! empty( $remove_tags ) ) {
                                    foreach ( $remove_tags as $remove_tag ) {
                                        $slug = sanitize_title( $remove_tag );
                                        $term = get_term_by( 'slug', $slug, $current_taxonomy );
                                        if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
                                            wp_delete_term( $term->term_id, $current_taxonomy );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if( isset( $filter['icon'] ) ) {
                        if( ! empty( $filter['icon'] ) ) {
                            $filters[$index]['icon'] = sanitize_text_field( $filter['icon'] );
                        }
                    }
                    if( isset( $filter['tags'] ) ) {
                        if( ! empty( $filter['tags'] ) ) {
                            $filters[$index]['tags'] = array_map( 'sanitize_text_field', $filter['tags'] );
                        }
                    }
                }
            }
        }
        return $filters;
    }

    public function update_awsm_jobs_remove_filters( $old_value, $new_value ) {
        $filters = $new_value;
        if( ! empty( $filters ) ) {
            foreach( $filters as $filter ) {
                if( taxonomy_exists( $filter ) ) {
                    $terms = get_terms( $filter, 'orderby=id&hide_empty=0' );
                    if( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                        foreach ( $terms as $term ) {
                            wp_delete_term( $term->term_id, $filter );
                        }
                    }
                }
            }
        }
    }

    public function sanitize_upload_file_extns( $input ) {
        $valid = true;
        $all_extns = $this->file_upload_extensions();
        foreach($input as $ext ) {
            if( ! in_array( $ext, $all_extns ) ) {
                $valid = false;
                break;
            }
        }
        if( ! $valid ) {
            add_settings_error( 'awsm_jobs_admin_upload_file_ext', 'awsm-upload-file-extension', esc_html__( 'Error in saving file upload types!', 'wp-job-openings' ) );
            return false;
        }
        return array_map( 'sanitize_text_field', $input);
    }

    public function awsm_gdpr_cb_text_handle( $input ) {
        $gdpr_enable = get_option( 'awsm_enable_gdpr_cb' );
        if( ! empty( $gdpr_enable ) && empty( $input ) ) {
            $input = esc_html__( 'By using this form you agree with the storage and handling of your data by this website.', 'wp-job-openings' );
        }
        return htmlentities( $input, ENT_QUOTES );
    }

    public function update_awsm_page_listing( $old_value, $value ){
        $page_id = $value;
        if ( ! empty( $page_id ) ) {
            $post_content = get_post_field( 'post_content', $page_id );
            if( ! has_shortcode( $post_content, 'awsmjobs' ) ) {
                $post_content .= '<p>[awsmjobs]</p>';
            }
            $page_data = array(
              'ID'           => $page_id,
              'post_content' => $post_content
            );
            wp_update_post( $page_data );
        }
    }

    public function update_awsm_permalink_slug( $old_value, $value ){
        if ( empty( $value ) ) {
            update_option( 'awsm_permalink_slug', 'jobs' );
        }
        $this->awsm_core->unregister_awsm_job_openings_post_type();
        $this->awsm_core->register_post_types();
        flush_rewrite_rules();
    }

    public function update_awsm_jobs_make_specs_clickable( $old_value, $value ) {
        if( ! empty( $value ) ) {
            flush_rewrite_rules();
        }
    }

    public function display_check_list( $label, $option_name, $value, $saved_data ) {
        $checked = '';
        if( ! empty( $saved_data ) ) {
            if( in_array( $value, $saved_data ) ) {
                $checked = ' checked';
            }
        }
        printf( '<li><label for="%1$s-%2$s"><input type="checkbox" name="%1$s[]" id="%1$s-%2$s" value="%2$s"%4$s />%3$s</label></li>', $option_name, $value, $label, $checked );
    }

    public function is_settings_field_checked( $option, $value, $default = false ) {
        $checked = '';
        if( ! empty( $option ) ) {
            if( $option === $value ) {
                $checked = 'checked';
            }
        } else {
            if( $default ) {
                $checked = 'checked';
            }
        }
        return $checked;
    }

    public function settings_switch_ajax() {
        if( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'awsm-admin-nonce' ) ) {
            wp_die();
        }
        if ( ! current_user_can( 'manage_awsm_jobs' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to manage options.', 'wp-job-openings' ) );
        }
        if( isset( $_POST['option'], $_POST['option_value'] ) ) {
            $option = sanitize_text_field( $_POST['option'] );
            $option_value = sanitize_text_field( $_POST['option_value'] );
            if( ! empty( $option ) ) {
                update_option( $option, $option_value );
            }
            echo $option_value;
        }
        wp_die();
    }
}