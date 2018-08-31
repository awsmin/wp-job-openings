<?php
    if( ! defined( 'ABSPATH' ) ) {
		exit;
	}
    $form_title = apply_filters( 'awsm_application_form_title', __( 'Apply for this position', 'wp-job-openings' ) );
    $form_submit_text = apply_filters( 'awsm_application_form_submit_btn_text', __( 'Submit', 'wp-job-openings' ) );
    $form_submit_res_text = apply_filters( 'awsm_application_form_submit_btn_res_text', __( 'Submitting..', 'wp-job-openings' ) );
?>

<div class="awsm-job-form-inner">
    <?php do_action( 'before_awsm_application_form' ); ?>
    <h2><?php echo esc_html( $form_title ); ?></h2>
    <form id="awsm-application-form" name="applicationform" method="post" enctype="multipart/form-data">
        <?php do_action( 'awsm_application_form_field_init' ); ?>
        <input type="hidden" name="awsm_job_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
        <?php wp_nonce_field( 'awsm_insert_application_nonce', 'awsm_application_nonce' ); ?>
        <input type="hidden" name="action" value="awsm_applicant_form_submission" >
        <div class="awsm-job-form-group">
            <input type="submit" name="form_sub" id="awsm-application-submit-btn" value="<?php echo esc_attr( $form_submit_text ); ?>" data-response-text="<?php echo esc_attr( $form_submit_res_text ); ?>" />
        </div>
    </form>
    <div class="awsm-application-message" style="display: none;"></div>
    <?php do_action( 'after_awsm_application_form' ); ?>
</div><!-- .awsm-job-form-inner -->