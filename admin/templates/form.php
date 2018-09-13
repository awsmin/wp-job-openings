<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $form_subtab = get_option( 'awsm_current_form_subtab', 'awsm-general-form-nav-subtab' );
    $gdpr_cb_text = get_option( 'awsm_gdpr_cb_text' );
    $recaptcha_site_key = get_option( 'awsm_jobs_recaptcha_site_key' );
    $recaptcha_secret_key = get_option( 'awsm_jobs_recaptcha_secret_key' );
?>
<!-- Upload File Extensions -->
<div id="settings-awsm-settings-form" class="awsm-admin-settings" style="display: none;">
    <?php do_action( 'awsm_settings_form_elem_start', 'form' ); ?>
    <form method="POST" action="options.php#settings-awsm-settings-form" id="upload-file-form">
        <?php settings_fields( 'awsm-jobs-form-settings' ); ?>

        <div class="awsm-nav-subtab-container clearfix">
            <ul class="subsubsub">
                <li>
                    <a href="#" class="awsm-nav-subtab current" id="awsm-general-form-nav-subtab" data-target="#awsm-general-form-options-container"><?php _e( 'General', 'wp-job-openings' ); ?></a>
                </li>
                <li>
                    <a href="#" class="awsm-nav-subtab" id="awsm-recaptha-form-nav-subtab" data-target="#awsm-recaptcha-form-options-container"><?php _e( 'reCAPTCHA', 'wp-job-openings' ); ?></a>
                </li>
                <?php do_action( 'awsm_jobs_settings_subtab_section', 'form' ); ?>
            </ul>
            <input type="hidden" name="awsm_current_form_subtab" class="awsm_current_settings_subtab" value="<?php echo esc_attr( $form_subtab ); ?>" />
        </div>

        <?php do_action( 'before_awsm_settings_main_content', 'form' ); ?>

        <div class="awsm-form-section-main awsm-sub-options-container" id="awsm-general-form-options-container">
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

        <div class="awsm-form-section-main awsm-sub-options-container" id="awsm-recaptcha-form-options-container" style="display: none;">
            <table class="form-table">
                <tbody>
                    <?php do_action( 'before_awsm_form_recaptcha_settings' ); ?>
                    <tr>
                        <th scope="row" colspan="2" class="awsm-form-head-title">
                            <h2><?php esc_html_e( 'reCAPTCHA options', 'wp-job-openings' ); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Enable reCAPTCHA', 'wp-job-openings' ); ?>
                        </th>
                        <td>
                            <label for="awsm_jobs_enable_recaptcha">
                                <input type="checkbox" name="awsm_jobs_enable_recaptcha" id="awsm_jobs_enable_recaptcha" value="enable" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_enable_recaptcha' ), 'enable' ) ); ?>><?php esc_html_e( 'Enable reCAPTCHA on the form', 'wp-job-openings' ); ?>
                            </label>

                             <a class="button awsm-view-captcha-btn" href="<?php echo esc_url( 'https://www.google.com/recaptcha/intro/index.html' ); ?>" target="__blank"><?php esc_html_e( 'Get your keys', 'wp-job-openings' ); ?></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Site key', 'wp-job-openings' ); ?>
                        </th>
                        <td>
                            <label for="awsm_jobs_recaptcha_site_key">
                                <input type="text" name="awsm_jobs_recaptcha_site_key" class="regular-text" value="<?php echo esc_attr( $recaptcha_site_key ); ?>">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Secret key', 'wp-job-openings' ); ?>
                        </th>
                        <td>
                            <label for="awsm_jobs_recaptcha_secret_key">
                                <input type="text" name="awsm_jobs_recaptcha_secret_key" class="regular-text" value="<?php echo esc_attr( $recaptcha_secret_key ); ?>">
                            </label>
                        </td>
                    </tr>
                    <?php do_action( 'after_awsm_form_recaptcha_settings' ); ?>
                </tbody>
            </table>
        </div>

        <?php do_action( 'after_awsm_settings_main_content', 'form' ); ?>

        <div class="awsm-form-footer">
        <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'form' ); ?>
        </div><!-- .awsm-form-footer -->
    </form>
    <?php do_action( 'awsm_settings_form_elem_end', 'form' ); ?>
</div><!-- .awsm-admin-settings -->