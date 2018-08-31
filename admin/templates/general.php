<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $default_listing_page_id = get_option( 'awsm_jobs_default_listing_page_id' );
    $selected_listing_page_id = get_option( 'awsm_select_page_listing', $default_listing_page_id );
    $permalink_slug = get_option( 'awsm_permalink_slug' );
    $hr_email_address = get_option( 'awsm_hr_email_address', '' );
    $awsm_company_name = get_option( 'awsm_job_company_name', '' );
    $default_message = get_option( 'awsm_default_msg' );

    $selected_page_status = get_post_status( $selected_listing_page_id );
    $page_exists = ( $selected_page_status === 'publish' ) ? true : false;
    $args = array(
        'id'       => 'awsm_select_page_listing',
        'name'     => 'awsm_select_page_listing',
        'class'    => 'awsm-select-page-control regular-text',
        'selected' => $selected_listing_page_id
    );
    if( ! $page_exists ) {
        $args['selected'] = '';
        $args['show_option_none'] = esc_html__( 'Select a page', 'wp-job-openings' );
    }
?>

<div id="settings-awsm-settings-general" class="awsm-admin-settings" style="display: block;" >
    <form method="POST" action="options.php#settings-awsm-settings-general" id="general_settings_form">
    	<?php
    	   settings_fields( 'awsm-jobs-general-settings' );
    	?>
    	<div class="awsm-form-section-main">
            <table class="form-table">
                <tbody>
                    <?php do_action( 'before_awsm_general_settings' ); ?>
                    <tr>
                        <th scope="row">
                            <label for="awsm_select_page_listing"><?php _e( 'Job listing page', 'wp-job-openings' ); ?></label>
                        </th>
                        <td>
                            <?php
                                wp_dropdown_pages( $args );
                                if( $page_exists ) :
                            ?>
                                <a class="button awsm-view-page-btn" href="<?php echo esc_url( get_page_link( $selected_listing_page_id ) ); ?>"><?php esc_html_e( 'View Page', 'wp-job-openings' ); ?></a>
                            <?php endif; ?>
                            <p class="description"><?php esc_html_e( 'The job listing shortcode will be added to  the page you select', 'wp-job-openings' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                           <label for="awsm-job-company-name"><?php _e( 'Name of the Company', 'wp-job-openings' ); ?>
                            </label>
                        </th>
                        <td>
                           <input type="text" class="regular-text" name="awsm_job_company_name" id="awsm-job-company-name" value="<?php echo esc_attr( $awsm_company_name ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="awsm-hr-email-address"><?php _e( 'HR Email Address', 'wp-job-openings' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="email" class="regular-text" name="awsm_hr_email_address" id="awsm-hr-email-address" value="<?php echo esc_attr( $hr_email_address ); ?>" />
                            <p class="description"><?php esc_html_e( 'Email for HR notifications', 'wp-job-openings' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                           <label for="awsm_permalink_slug"><?php _e( 'URL slug', 'wp-job-openings' ); ?></label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="awsm_permalink_slug" id="awsm_permalink_slug" value="<?php echo esc_attr( $permalink_slug ); ?>" required />
                            <p class="description"><?php esc_html_e( 'URL slug for job posts', 'wp-job-openings' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                           <label for="awsm_default_msg"><?php _e( "Default 'No Jobs' message "  , 'wp-job-openings' ); ?>
                        </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="awsm_default_msg" id="awsm_default_msg" value="<?php echo esc_attr( $default_message ); ?>" required />
                            <p class="description"><?php esc_html_e( 'Default message when there are no active job openings', 'wp-job-openings' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                          <?php _e( 'Delete data on uninstall', 'wp-job-openings' ) ?>
                        </th>
                        <td>
                            <label for="awsm-delete-data-on-uninstall" class="awsm-text-danger">
                                <input type="checkbox" name="awsm_delete_data_on_uninstall" id="awsm-delete-data-on-uninstall" value="delete_data" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_delete_data_on_uninstall' ), 'delete_data' ) ); ?> /><?php _e( 'Delete PLUGIN DATA on uninstall', 'wp-job-openings' ); ?>
                            </label>
                            <p class="description"><?php printf( esc_html__( 'CAUTION: Checking this option will delete all the job listings, applications and %sconfigurations from your website %swhen you uninstall the plugin%s.', 'wp-job-openings' ), '<br />', '<span>', '</span>' ); ?></p>
                        </td>
                    </tr>
                    <?php do_action( 'after_awsm_general_settings' ); ?>
                </tbody>
            </table>
        </div>
        <div class="awsm-form-footer">
        <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'general' ); ?>
        </div><!-- .awsm-form-footer -->
	</form>
</div>