<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $expired_message = esc_html__( 'Sorry! This job is expired.', 'wp-job-openings' );
?>

<div class="awsm-job-single-wrap<?php echo AWSM_Job_Openings::get_job_details_class(); ?>">
    <?php do_action( 'before_awsm_job_details' ); ?>

    <?php if( get_option( 'awsm_jobs_expired_jobs_content_details' ) != 'content' || get_post_status() != 'expired' ) : ?>
        <div class="awsm-job-content">
            <?php include_once untrailingslashit( plugin_dir_path( __FILE__ ) ) .  '/partials/job-details.php'; ?>
        </div><!-- .awsm-job-content -->

        <div class="awsm-job-form">
            <?php
                if( get_post_status() != 'expired' ) {
                    do_action( 'awsm_application_form_init' );
                } else {
                    printf( '<div class="awsm-job-form-inner">%s</div>', $expired_message );
                }
            ?>
        </div><!-- .awsm-job-form -->
    <?php else : ?>
        <div class="awsm-expired-message">
            <p><?php echo $expired_message; ?></p>
        </div>
    <?php endif; ?>

    <?php do_action( 'after_awsm_job_details' ); ?>
</div><!-- .awsm-job-single-wrap -->