<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$applicant_email = esc_attr( get_post_meta( $post->ID, 'awsm_applicant_email', true ) );
$applicant_details = $this->get_applicant_meta_details_list( $post->ID, array( 'awsm_applicant_email' => $applicant_email ) );

$tab_applicant_single_view  = AWSM_Job_Openings_Meta::set_applicant_single_view_tab();
$attachment_id  = get_post_meta( $post->ID, 'awsm_attachment_id', true );
$resume_details = $this->get_attached_file_details( $attachment_id );
$full_file_name = get_post_meta( $attachment_id, 'awsm_actual_file_name', true );

$job_id = get_post_meta( $post->ID, 'awsm_job_id', true);

$form_meta = get_post_meta($job_id, 'awsm_pro_application_form', true);

if ( ! empty( $form_meta ) ) {
    $form_data = maybe_unserialize( $form_meta ); 

    if ( is_array( $form_data ) && isset( $form_data['id'] ) ) {
        $form_id = $form_data['id'];
    } else {
        $form_id = null; 
    }
} else {
    $form_id = 'default'; 
}
$file_label  = AWSM_Job_Openings_Form::get_awsm_file_label( $form_id, $job_id );

/**
 * Initialize applicant meta box.
 *
 * @since 1.6.0
 */
do_action( 'awsm_job_applicant_mb_init', $post->ID );
?>

<div class="awsm-application-container awsm-clearfix">
    <div class="awsm-application-main">
        <div class="awsm-application-head">
            <div class="awsm-application-head-main">
                <?php
                /**
                 * Fires before applicant photo content.
                 *
                 * @since 1.6.0
                 */
                do_action( 'before_awsm_job_applicant_mb_photo', $post->ID );

                $avatar = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 130 ) );
                echo '<div class="awsm-applicant-image">' . $avatar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
                <div class="awsm-applicant-info">
                    <h3><?php echo esc_html( $applicant_details['name'] ); ?></h3>
                    <?php $title = esc_html( sprintf( get_post_meta( $post->ID, 'awsm_apply_for', true ) ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                    <p><?php esc_html_e( $title, 'wp-job-openings' ); ?></p>
                </div>
                <!-- .awsm-applicant-info -->
                <?php
                /**
                 * Fires after applicant photo content.
                 *
                 * @since 1.6.0
                 */
                do_action( 'after_awsm_job_applicant_mb_photo', $post->ID );
                ?>
            </div>
            <!-- .awsm-application-head-main -->
            <div class="awsm-application-actions <?php if(! class_exists( 'AWSM_Job_Openings_Pro_Pack' )){echo 'pro-feature'; }?>">
                <span class="pro-ft"><?php esc_html_e( 'Pro Features', 'wp-job-openings' ); ?></span>
                <?php 
                if (class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
                    do_action('awsm_application_rating'); 
                } else { ?>
                    <div class="awsm-application-rating-disabled">
                            <?php
                                wp_star_rating(
                                    array(
                                        'rating' => 3,
                                        'type'   => 'rating',
                                    )
                                );
                                ?>
                        </div>
                <?php } ?>
                    <div class="awsm-application-action">
                        <a class="awsm-application-action-btn" href="#">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.40008 1.60001C6.40008 2.48367 7.11642 3.20002 8.00009 3.20002C8.88375 3.20002 9.6001 2.48367 9.6001 1.60001C9.6001 0.716349 8.88375 -3.13126e-08 8.00009 -6.99387e-08C7.11642 -1.08565e-07 6.40008 0.716349 6.40008 1.60001Z" fill="#161616"/>
                                <path d="M6.40008 7.99997C6.40008 8.88364 7.11642 9.59999 8.00009 9.59999C8.88375 9.59999 9.6001 8.88364 9.6001 7.99997C9.6001 7.11631 8.88375 6.39996 8.00009 6.39996C7.11642 6.39996 6.40008 7.11631 6.40008 7.99997Z" fill="#161616"/>
                                <path d="M6.40008 14.4C6.40008 15.2837 7.11642 16 8.00009 16C8.88375 16 9.6001 15.2837 9.6001 14.4C9.6001 13.5163 8.88375 12.8 8.00009 12.8C7.11642 12.8 6.40008 13.5163 6.40008 14.4Z" fill="#161616"/>
                            </svg>
                        </a>
                        <?php 
                        if (class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ){ ?>
                            <div class="awsm-application-action-list">
                            <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $post->ID . '&action=edit&application=edit&form-id=' . $form_id . '&job-id=' . $job_id ) ); ?>" 
                                id="awsm-button-edit-application">
                                <?php esc_html_e( 'Edit Profile', 'pro-pack-for-wp-job-openings' ); ?>
                            </a>

                                <?php do_action( 'after_awsm_job_applicant_mb_details_list', $post->ID ); ?>
                            </div><!-- .awsm-application-action-list -->
                        <?php } ?>           
                    </div><!-- .awsm-application-action -->
                
                <!-- Action End -->
            </div>
            <!-- .awsm-application-head-actions -->
        </div>
        <!-- .awsm-application-head -->
        <div class="application-main-cnt">
            <?php

                if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['application']) && $_GET['application'] === 'edit') {
                    
                    if (isset($_GET['form-id'])) {
                        $form_id = sanitize_text_field($_GET['form-id']); 
                       
                        do_action('awsm_job_applicantion_edit', $form_id);
                    } else {
                        
                        echo "Form ID is missing in the URL.";
                    }
                }
                else{
                
            ?>
            <ul class="application-main-tab">
                <?php foreach ($tab_applicant_single_view as $key => $tab_data): ?>
                    <li>
                        <a href="#awsm-applicant-<?php echo esc_attr($key); ?>" <?php echo $key === 'profile' ? 'class="active"' : ''; ?>>
                            <?php 
                            if (is_array($tab_data) && isset($tab_data['label'])) {
                                echo esc_html($tab_data['label']); 
                                if (isset($tab_data['count'])) {
                                    echo '<span>' . intval($tab_data['count']) . '</span>';
                                }
                            } else {
                                echo esc_html($tab_data);
                            }
                            ?>                        
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="application-main-tab-items">
                <div id="awsm-applicant-profile" class="application-main-tab-item awsm-applicant-profile active">
                    <?php
                    /**
                     * Fires before applicant details list.
                     *
                     * @since 1.6.0
                     */
                    do_action( 'before_awsm_job_applicant_mb_details_list', $post->ID );
                    ?>
                    <ul class="awsm-applicant-details-list">
                        <?php echo wp_kses_post( $applicant_details['list'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php
                        if ( ! empty( $resume_details ) ) :
                            $file_size_display = ! empty( $resume_details['file_size']['display'] ) ? $resume_details['file_size']['display'] : ''; ?>
                            <li>
                                <label>
                                    <?php 
                                        if (!empty($file_label)) {
                                            echo esc_html($file_label);
                                        } else {
                                            echo esc_html__('Resume', 'wp-job-openings');
                                        }
                                    ?>
                                </label>
                                <div class="awsm-applicant-resume">
                                    <span class="hs-reume-info">
                                        <span>
                                            <strong>
                                                <?php 
                                                if(!empty($full_file_name)){
                                                    esc_html_e( $full_file_name, 'wp-job-openings' );
                                                }else{
                                                    esc_html_e( 'Resume.pdf', 'wp-job-openings' ); 
                                                }
                                                ?>
                                            </strong>
                                            <?php echo esc_html( $file_size_display ); ?>
                                        </span>
                                    </span>
                                    
                                    <div class="awsm-applicant-resume-cta">
                                        <?php
                                            /**
                                             * Fires after applicant details list.
                                             *
                                             * @since 1.6.0
                                             */
                                            
                                            do_action( 'after_awsm_job_applicant_details_list_preview_resume', $post->ID );
                                            
                                        ?>
                                        <a href="<?php echo esc_url( $this->get_attached_file_download_url( $attachment_id ) ); ?>"  rel="nofollow">
                                            <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin">
                                                <path xmlns="http://www.w3.org/2000/svg" fill="black" d="M8.66667 7.99998H10.6667L8 10.6666L5.33333 7.99998H7.33333V5.33331H8.66667V7.99998ZM10 2.66665H3.33333V13.3333H12.6667V5.33331H10V2.66665ZM2 1.99451C2 1.62935 2.29833 1.33331 2.66567 1.33331H10.6667L13.9998 4.66665L14 13.995C14 14.3659 13.7034 14.6666 13.3377 14.6666H2.66227C2.29651 14.6666 2 14.3631 2 14.0054V1.99451Z"
                                                />
                                            </svg>
                                            <?php esc_html_e( 'Download', 'wp-job-openings' ); ?>
                                        </a>
                                        
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                </div>
                <div id="awsm-applicant-email" class="application-main-tab-item awsm-application-email">
                <?php            
                    do_action( 'awsm_job_applicant_profile_details_email', $post->ID );
                ?>
                </div>
                <div id="awsm-applicant-resume" class="application-main-tab-item awsm-application-resume">
                    <?php
                        //do_action( 'awsm_job_applicant_profile_details_resume_preview', $attachment_id );
                    ?>
                </div>
                <div id="awsm-applicant-notes" class="application-main-tab-item awsm-application-notes">
                    <?php
                        do_action( 'awsm_job_applicant_profile_details_note', $post->ID );
                    ?> 
                </div>
            </div>
            <?php } ?>
            <!-- .application-main-tab-items -->
        </div>
        <!-- .application-main-cnt -->
    </div>
    <!-- .awsm-application-main -->
    <?php
    // Compatibility fix for Pro version.
    if ( defined( 'AWSM_JOBS_PRO_PLUGIN_VERSION' ) && version_compare( AWSM_JOBS_PRO_PLUGIN_VERSION, '1.4.0', '<' ) ) :
        ?>
        <div class="submitbox awsm-application-submitbox">
            <div id="major-publishing-actions" class="awsm-application-major-actions awsm-clearfix">
                <?php $this->application_delete_action( $post->ID ); ?>
            </div>
            <!-- #major-publishing-actions -->
        </div>
        <!-- .awsm-application-submitbox -->
        <?php
    endif;
    ?>
</div>
