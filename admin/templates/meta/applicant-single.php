<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$applicant_email = esc_attr( get_post_meta( $post->ID, 'awsm_applicant_email', true ) );
$applicant_details = $this->get_applicant_meta_details_list( $post->ID, array( 'awsm_applicant_email' => $applicant_email ) );
$tab_applicant_single_view  = AWSM_Job_Openings_Meta::set_applicant_single_view_tab();

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
                        $attachment_id  = get_post_meta( $post->ID, 'awsm_attachment_id', true );
                        $resume_details = $this->get_attached_file_details( $attachment_id );
                        $full_file_name = get_post_meta( $attachment_id, 'actual_file_name', true );
                        
                        if ( ! empty( $resume_details ) ) :
                            $file_size_display = ! empty( $resume_details['file_size']['display'] ) ? $resume_details['file_size']['display'] : ''; ?>
                            <li>
                                <label>
                                    <?php 
                                    esc_html_e( 'Resume', 'wp-job-openings' ); ?>
                                    </label>
                                <div class="awsm-applicant-resume">
                                    <span class="hs-reume-info">
                                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" fill="#FF0000" d="M19.8545 7.81594L19.8545 19.8418C19.8545 20.1908 19.7838 20.5364 19.6464 20.8588C19.509 21.1812 19.3076 21.4742 19.0538 21.721C18.7999 21.9678 18.4986 22.1635 18.1669 22.2971C17.8352 22.4306 17.4798 22.4994 17.1208 22.4994H6.87854C6.51955 22.4994 6.16407 22.4306 5.8324 22.2971C5.50073 22.1635 5.19937 21.9678 4.94552 21.721C4.69167 21.4742 4.49031 21.1812 4.35293 20.8588C4.21555 20.5364 4.14484 20.1908 4.14484 19.8418V4.15759C4.14484 3.80859 4.21555 3.46301 4.35293 3.14057C4.49031 2.81814 4.69167 2.52517 4.94552 2.27839C5.19937 2.03161 5.50073 1.83585 5.8324 1.70229C6.16407 1.56874 6.51955 1.5 6.87854 1.5L13.39 1.50002L19.8545 7.81594Z"/><path xmlns="http://www.w3.org/2000/svg" fill="#D40000" d="M13.6741 1.77759C13.1499 3.12697 11.9367 5.19574 13.16 6.93463C13.4778 7.41694 15.4138 8.1625 16.6288 8.59273C17.4044 8.86735 18.0423 9.73301 18.0423 10.5574L18.1441 18.5844C18.1441 20.0522 16.921 21.2414 15.4112 21.2414H5.16899C4.9412 21.2414 4.72011 21.2135 4.50854 21.1624C4.99502 21.9888 5.89911 22.4991 6.87855 22.5H17.1207C18.6312 22.5004 19.8556 21.3099 19.8551 19.8415V7.81539L13.6741 1.77759Z"/><path xmlns="http://www.w3.org/2000/svg" fill="white" d="M5.55836 3.13282C5.66098 3.13241 5.7596 3.1726 5.8327 3.24462C5.90581 3.31665 5.94746 3.41467 5.94857 3.51728V4.60756C5.94877 4.65886 5.93881 4.70969 5.91927 4.75713C5.89974 4.80456 5.871 4.84766 5.83473 4.88393C5.79845 4.9202 5.75536 4.94894 5.70792 4.96848C5.66049 4.98802 5.60966 4.99797 5.55836 4.99777C5.45574 4.99666 5.35773 4.95501 5.2857 4.88191C5.21367 4.80881 5.17348 4.71019 5.17389 4.60756V3.51728C5.17499 3.41566 5.21584 3.3185 5.28771 3.24663C5.35958 3.17477 5.45673 3.13391 5.55836 3.13282Z"/><path xmlns="http://www.w3.org/2000/svg" fill="white" d="M5.55836 5.25598C5.60966 5.25578 5.66049 5.26574 5.70792 5.28527C5.75536 5.30481 5.79845 5.33355 5.83473 5.36982C5.871 5.4061 5.89974 5.44919 5.91927 5.49663C5.93881 5.54406 5.94877 5.59489 5.94857 5.64619V5.80686C5.94746 5.90948 5.90581 6.00749 5.83271 6.07952C5.7596 6.15155 5.66098 6.19174 5.55836 6.19133C5.45673 6.19024 5.35958 6.14938 5.28771 6.07751C5.21584 6.00565 5.17499 5.90849 5.17389 5.80686V5.64619C5.17348 5.54357 5.21367 5.44495 5.2857 5.37185C5.35773 5.29874 5.45574 5.25709 5.55836 5.25598Z"/><path xmlns="http://www.w3.org/2000/svg" fill="#FF8080" d="M13.3903 1.50031L12.5408 4.8202C12.4843 5.04103 12.4752 5.2709 12.5141 5.49534C12.553 5.71978 12.639 5.93392 12.7667 6.12428C12.8945 6.31464 13.0612 6.4771 13.2564 6.60143C13.4515 6.72576 13.6709 6.80927 13.9007 6.84669L19.8548 7.81623L13.3903 1.50031Z"/><path xmlns="http://www.w3.org/2000/svg" fill="white" d="M12.0159 9.8562C11.5634 9.86266 11.2486 10.1505 11.0958 10.3223C10.7415 10.7208 10.7253 11.2671 10.8438 11.7965C10.9623 12.326 11.216 12.8896 11.5255 13.4606C11.5602 13.5246 11.5987 13.5679 11.6348 13.6321C11.0124 14.7648 10.1563 15.9979 9.40905 16.9814C8.43892 17.212 7.51039 17.3252 6.9525 17.9531C6.48537 18.4795 6.4529 19.2793 6.9525 19.7446C7.16983 19.9354 7.47387 20.0321 7.73897 20.0362C7.98792 20.0333 8.20039 19.9624 8.37056 19.8585C9.02278 19.4603 9.4475 18.6148 9.91767 17.6373L13.73 16.7491C14.302 17.4581 14.8972 18.1531 15.6066 18.3721C15.9355 18.4737 16.2828 18.4617 16.5935 18.3494C16.9041 18.237 17.1944 18.0148 17.3268 17.6722C17.5578 17.0498 17.3031 16.3603 16.7073 16.049C16.2448 15.7995 15.6685 15.757 15.0266 15.7849C14.6986 15.7991 14.3521 15.8342 14.0003 15.8851C13.5844 15.2514 13.0248 14.414 12.5367 13.5986C12.823 13.0302 13.0717 12.4708 13.1789 11.9573C13.2985 11.3841 13.2746 10.8194 12.9496 10.3844C12.7165 10.016 12.3608 9.8586 12.0159 9.8562ZM12.3286 10.8385C12.4701 11.021 12.5181 11.3325 12.4213 11.7965C12.3675 12.0544 12.1853 12.4005 12.0539 12.7151C11.8654 12.3267 11.6678 11.9286 11.5999 11.625C11.5087 11.2175 11.5714 10.9917 11.6758 10.8385C11.7492 10.7187 11.8486 10.6452 11.9912 10.637C12.1414 10.6286 12.2411 10.7255 12.3286 10.8385ZM12.0933 14.3503C12.4587 14.9323 12.8476 15.5379 13.1652 16.0295L10.6145 16.6489C11.129 15.8685 11.6688 15.0721 12.0933 14.3503ZM16.3354 16.7309C16.6158 16.8822 16.7001 17.1219 16.598 17.3913C16.502 17.6396 16.0397 17.7063 15.8358 17.6327C15.559 17.5473 15.1129 17.0341 14.7047 16.611C14.8176 16.583 14.9549 16.564 15.0615 16.5593C15.4719 16.5413 15.9524 16.5509 16.3354 16.7309ZM8.71368 18.0563C8.44376 18.4951 8.13974 19.0912 7.96214 19.1996C7.78653 19.3204 7.58379 19.2754 7.48085 19.1707C7.33856 19.0335 7.31404 18.7238 7.53854 18.4708C7.76733 18.2507 8.0942 18.1587 8.71368 18.0563Z"/></svg>
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
                                            
                                            //do_action( 'after_awsm_job_applicant_mb_details_list', $post->ID );
                                            do_action( 'after_awsm_job_applicant_details_list_preview_resume', $post->ID );
                                            
                                        ?>
                                        <a href="<?php echo esc_url( $this->get_attached_file_download_url( $attachment_id ) ); ?>">
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
                        do_action( 'awsm_job_applicant_profile_details_resume_preview', $post->ID );
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
