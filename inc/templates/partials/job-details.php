<?php

    if( ! defined( 'ABSPATH' ) ) {
		exit;
	}

    do_action( 'awsm_application_form_notices' );

    $expiry_details = AWSM_Job_Openings::get_job_expiry_details( get_the_ID(), get_post_status() );
    if( ! empty( $expiry_details ) ) {
        printf( '<div class="awsm-job-head">%s</div>', $expiry_details );
    }

    AWSM_Job_Openings::display_specifications_content( get_the_ID(), 'above_content' );
?>

<div class="awsm-job-entry-content entry-content">
    <?php echo $content; ?>
</div><!-- .awsm-job-entry-content -->

<?php AWSM_Job_Openings::display_specifications_content( get_the_ID(), 'below_content' ); ?>