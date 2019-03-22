<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $default_listing_page_id = get_option( 'awsm_jobs_default_listing_page_id' );
    $selected_listing_page_id = get_option( 'awsm_select_page_listing', $default_listing_page_id );
    $selected_page_status = get_post_status( $selected_listing_page_id );
    $page_exists = ( $selected_page_status === 'publish' ) ? true : false;
    $args = array(
        'echo'     => false,
        'id'       => 'awsm_select_page_listing',
        'name'     => 'awsm_select_page_listing',
        'class'    => 'awsm-select-page-control regular-text',
        'selected' => $selected_listing_page_id
    );
    if( ! $page_exists ) {
        $args['selected'] = '';
        $args['show_option_none'] = esc_html__( 'Select a page', 'wp-job-openings' );
    }
    
    /**
     * Filters the general settings fields.
     *
     * @since 1.4
     * 
     * @param array $settings_fields General Settings fields
     */
    $settings_fields = apply_filters( 'awsm_jobs_general_settings_fields', array( 
        'default' => array(
            'awsm_select_page_listing' => array(
                'label'        => __( 'Job listing page', 'wp-job-openings' ),
                'type'         => 'raw',
                'value'        => wp_dropdown_pages( $args ),
                'description'  => __( 'The job listing shortcode will be added to  the page you select', 'wp-job-openings' ),
                'help_button'  => array(
                    'visible'  => $page_exists,
                    'url'      => get_page_link( $selected_listing_page_id ),
                    'class'    => 'awsm-view-page-btn',
                    'text'     => __( 'View Page', 'wp-job-openings' ),
                ),
            ),
            'awsm_job_company_name' => array(
                'label'        => __( 'Name of the Company', 'wp-job-openings' ),
                'id'           => 'awsm-job-company-name',
                'value'        => get_option( 'awsm_job_company_name', '' ),
            ),
            'awsm_hr_email_address' => array(
                'label'        => __( 'HR Email Address', 'wp-job-openings' ),
                'type'         => 'email',
                'id'           => 'awsm-hr-email-address',
                'value'        => get_option( 'awsm_hr_email_address', '' ),
                'description'  => __( 'Email for HR notifications', 'wp-job-openings' ),
            ),
            'awsm_permalink_slug' => array(
                'label'        => __( 'URL slug', 'wp-job-openings' ),
                'value'        => get_option( 'awsm_permalink_slug' ),
                'required'     => true,
                'description'  => __( 'URL slug for job posts', 'wp-job-openings' ),
            ),
            'awsm_default_msg' => array(
                'label'        => __( "Default 'No Jobs' message "  , 'wp-job-openings' ),
                'value'        => get_option( 'awsm_default_msg' ),
                'required'     => true,
                'description'  => __( 'Default message when there are no active job openings', 'wp-job-openings' ),
            ),
            'awsm_delete_data_on_uninstall' => array(
                'label'        => __( 'Delete data on uninstall', 'wp-job-openings' ),
                'type'         => 'checkbox',
                'class'        => '',
                'choices'      => array( 
                    array(
                        'value'       => 'delete_data',
                        'text'        => __( 'Delete PLUGIN DATA on uninstall', 'wp-job-openings' ),
                        'text_class'  => 'awsm-text-danger',
                        'id'          => 'awsm-delete-data-on-uninstall',
                    ),
                ),
                'value'        => get_option( 'awsm_delete_data_on_uninstall' ),
                'description'  => sprintf( __( 'CAUTION: Checking this option will delete all the job listings, applications and %sconfigurations from your website %swhen you uninstall the plugin%s.', 'wp-job-openings' ), '<br />', '<span>', '</span>' ),
            ), 
        ),
    ) );
?>

<div id="settings-awsm-settings-general" class="awsm-admin-settings">
    <?php do_action( 'awsm_settings_form_elem_start', 'general' ); ?>
    <form method="POST" action="options.php" id="general_settings_form">
    	<?php
           settings_fields( 'awsm-jobs-general-settings' );

           // display general subtabs.
           $this->display_subtabs( 'general' );

           do_action( 'before_awsm_settings_main_content', 'general' );
        ?>
        
    	<div class="awsm-form-section-main awsm-sub-options-container" id="awsm-general-options-container">
            <table class="form-table">
                <tbody>
                    <?php
                        do_action( 'before_awsm_general_settings' );

                        $this->display_settings_fields( $settings_fields['default'] );

                        do_action( 'after_awsm_general_settings' );
                    ?>
                </tbody>
            </table>
        </div>

        <?php do_action( 'after_awsm_settings_main_content', 'general' ); ?>

        <div class="awsm-form-footer">
        <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'general' ); ?>
        </div><!-- .awsm-form-footer -->
	</form>
    <?php do_action( 'awsm_settings_form_elem_end', 'general' ); ?>
</div>