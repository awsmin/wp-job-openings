<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $default_policy_page_id = get_option( 'wp_page_for_privacy_policy' );
    $gdpr_cb_text = get_option( 'awsm_gdpr_cb_text' );
?>
<!-- Upload File Extensions -->
<div id="settings-awsm-settings-form" class="awsm-admin-settings" style="display: none;">
    <form method="POST" action="options.php#settings-awsm-settings-form" id="upload-file-form">
        <?php settings_fields( 'awsm-jobs-form-settings' ); ?>
        <div class="awsm-form-section-main">
            <table class="form-table">
                <tbody>
                    <?php do_action( 'before_awsm_form_settings' ); ?>
                    <tr>
                        <th scope="row" colspan="2" class="awsm-form-head-title">
                            <h2><?php esc_html_e( 'Application form options', 'wp-job-openings'); ?></h2>
                        </th>
                    </tr>
                    <?php
                        $all_upload_file_extns = $this->file_upload_extensions();
                        $upload_file_extns = get_option( 'awsm_jobs_admin_upload_file_ext' );
                    ?>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Supported upload file types', 'wp-job-openings' ); ?>
                        </th>
                        <td>
                            <ul class="awsm-check-list awsm-check-list-small">
                                <?php
                                    foreach( $all_upload_file_extns as $extension ) {
                                        $this->display_check_list( $extension, 'awsm_jobs_admin_upload_file_ext', $extension, $upload_file_extns );
                                    }
                                ?>
                            </ul>
                            <p class="description"><?php esc_html_e( 'Select the supported file types for CV upload field', 'wp-job-openings' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" colspan="2" class="awsm-form-head-title">
                            <h2><?php echo esc_html__('GDPR Compliance', 'wp-job-openings'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                             <?php esc_html_e( 'The checkbox', 'wp-job-openings' ); ?>
                        <td>
                            <label for="awsm_enable_gdpr_cb"><input type="checkbox" name="awsm_enable_gdpr_cb" class="awsm-check-control-field" id="awsm_enable_gdpr_cb" value="true" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_enable_gdpr_cb' ), 'true' ) ); ?> data-req-target="#awsm_gdpr_cb_text" /> <?php _e( 'Enable the GDPR compliance checkbox', 'wp-job-openings' ) ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                             <label for="awsm_gdpr_cb_text"><?php esc_html_e( 'Checkbox text', 'wp-job-openings' ); ?></label>
                        <td>
                            <input type="text" class="medium-text" name="awsm_gdpr_cb_text" id="awsm_gdpr_cb_text" value="<?php echo $gdpr_cb_text; ?>" />
                        </td>
                    </tr>
                    <?php do_action( 'after_awsm_form_settings' ); ?>
                </tbody>
            </table>
        </div><!-- .awsm-form-section-main -->
        <div class="awsm-form-footer">
            <?php submit_button(); ?>
        </div><!-- .awsm-form-footer -->
    </form>
</div><!-- .awsm-admin-settings -->