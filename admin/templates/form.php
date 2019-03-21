<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    
    $upload_file_extns = $this->file_upload_extensions();
    $extns_choices = array();
    if( ! empty( $upload_file_extns ) ) {
        foreach( $upload_file_extns as $extension ) {
            $extns_choices[] = array(
                'value'       => $extension,
                'text'        => $extension,
            );
        }
    }

    /**
     * Filters the form settings fields.
     *
     * @since 1.4
     * 
     * @param array $settings_fields Form Settings fields
     */
    $settings_fields = apply_filters( 'awsm_jobs_form_settings_fields', array(
        'general' => array(
            'application_form_title' => array(
                'label'        => __( 'Application form options', 'wp-job-openings'),
                'type'         => 'title',
            ),
            'awsm_jobs_admin_upload_file_ext' => array(
                'label'           => __( 'Supported upload file types', 'wp-job-openings' ),
                'type'            => 'checkbox',
                'multiple'        => true,
                'class'           => '',
                'list_class'      => 'awsm-check-list awsm-check-list-small',
                'choices'         => $extns_choices,
                'value'           => get_option( 'awsm_jobs_admin_upload_file_ext' ),
                'description'     => __( 'Select the supported file types for CV upload field', 'wp-job-openings' ),
            ),
            'gdpr_compliance_title' => array(
                'label'        => __( 'GDPR Compliance', 'wp-job-openings' ),
                'type'         => 'title',
            ),
            'awsm_enable_gdpr_cb' => array(
                'label'           => __( 'The checkbox', 'wp-job-openings' ),
                'type'            => 'checkbox',
                'class'           => 'awsm-check-control-field',
                'choices'         => array( 
                    array(
                        'value'       => 'true',
                        'text'        => __( 'Enable the GDPR compliance checkbox', 'wp-job-openings' ),
                        'data_attrs'  => array(
                            array(
                                'attr'  => 'req-target',
                                'value' => '#awsm_gdpr_cb_text',
                            ),
                        ),
                    ),
                ),
                'value'           => get_option( 'awsm_enable_gdpr_cb' ),
            ),
            'awsm_gdpr_cb_text' => array(
                'label'           => __( 'Checkbox text', 'wp-job-openings' ),
                'class'           => 'medium-text',
                'value'           => get_option( 'awsm_gdpr_cb_text' ),
            ),
        ),
        'recaptcha' => array(
            'recaptcha_title' => array(
                'label'        => __( 'reCAPTCHA options', 'wp-job-openings' ),
                'type'         => 'title',
            ),
            'awsm_jobs_enable_recaptcha' => array(
                'label'        => __( 'Enable reCAPTCHA', 'wp-job-openings' ),
                'type'         => 'checkbox',
                'class'        => '',
                'choices'         => array( 
                    array(
                        'value'       => 'enable',
                        'text'        => __( 'Enable reCAPTCHA on the form', 'wp-job-openings' ),
                    ),
                ),
                'value'        => get_option( 'awsm_jobs_enable_recaptcha' ),
                'help_button'  => array(
                    'url'      => 'https://www.google.com/recaptcha/intro/index.html',
                    'class'    => 'awsm-view-captcha-btn',
                    'text'     => __( 'Get your keys', 'wp-job-openings' ),
                    'other_attrs' => array(
                        'target' => '_blank',
                    )
                ),
            ),
            'awsm_jobs_recaptcha_site_key' => array(
                'label'           => __( 'Site key', 'wp-job-openings' ),
                'value'           => get_option( 'awsm_jobs_recaptcha_site_key' ),
            ),
            'awsm_jobs_recaptcha_site_key' => array(
                'label'           => __( 'Site key', 'wp-job-openings' ),
                'value'           => get_option( 'awsm_jobs_recaptcha_site_key' ),
            ),
            'awsm_jobs_recaptcha_secret_key' => array(
                'label'           => __( 'Secret key', 'wp-job-openings' ),
                'value'           => get_option( 'awsm_jobs_recaptcha_secret_key' ),
            ),
        ),
    ) );
?>

<!-- Upload File Extensions -->
<div id="settings-awsm-settings-form" class="awsm-admin-settings">
    <?php do_action( 'awsm_settings_form_elem_start', 'form' ); ?>
    <form method="POST" action="options.php" id="upload-file-form">
        <?php
            settings_fields( 'awsm-jobs-form-settings' );

            // display form subtabs.
            $this->display_subtabs( 'form', 'awsm-general-form-nav-subtab' );

            do_action( 'before_awsm_settings_main_content', 'form' );
        ?>

        <div class="awsm-form-section-main awsm-sub-options-container" id="awsm-general-form-options-container">
            <table class="form-table">
                <tbody>
                    <?php
                        do_action( 'before_awsm_form_settings' );

                        $this->display_settings_fields( $settings_fields['general'] );

                        do_action( 'after_awsm_form_settings' );
                    ?>
                </tbody>
            </table>
        </div><!-- .awsm-form-section-main -->

        <div class="awsm-form-section-main awsm-sub-options-container" id="awsm-recaptcha-form-options-container" style="display: none;">
            <table class="form-table">
                <tbody>
                    <?php
                        do_action( 'before_awsm_form_recaptcha_settings' );

                        $this->display_settings_fields( $settings_fields['recaptcha'] );

                        do_action( 'after_awsm_form_recaptcha_settings' );
                    ?>
                </tbody>
            </table>
        </div><!-- .awsm-form-section-main -->

        <?php do_action( 'after_awsm_settings_main_content', 'form' ); ?>

        <div class="awsm-form-footer">
        <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'form' ); ?>
        </div><!-- .awsm-form-footer -->
    </form>
    <?php do_action( 'awsm_settings_form_elem_end', 'form' ); ?>
</div><!-- .awsm-admin-settings -->