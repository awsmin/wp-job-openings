<?php

    if( ! defined( 'ABSPATH' ) ) {
		exit;
	}
    do_action( 'awsm_application_form_notices' );

    $expiry_details = $this->get_job_expiry_details( get_the_ID(), get_post_status() );
    if( ! empty( $expiry_details ) ) {
        printf( '<div class="awsm-job-head">%s</div>', $expiry_details );
    }
?>

<div class="awsm-job-entry-content entry-content">
    <?php echo $content; ?>
</div><!-- .awsm-job-entry-content -->

<div class="awsm-job-specifications-container">
    <div class="awsm-job-specifications-row">
        <?php
            $awsm_filters = get_option( 'awsm_jobs_filter' );
            echo $this->get_specifications_content( get_the_ID(), true, $awsm_filters );
        ?>
    </div>
</div><!-- .awsm-job-details -->