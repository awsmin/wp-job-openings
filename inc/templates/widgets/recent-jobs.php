<?php
/**
 * Template part for recent jobs widget
 * 
 * Override this by copying it to currenttheme/wp-job-openings/widgets/recent-jobs.php
 * 
 * @package wp-job-openings
 * @version 1.4
 */

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

$awsm_filters = get_option( 'awsm_jobs_filter' );
$listing_specs = get_option( 'awsm_jobs_listing_specs' );

/**
 * before_awsm_jobs_listing_loop hook
 * 
 * Fires before The Loop to query for jobs
 * 
 * @since 1.1
 */
do_action( 'before_awsm_recent_jobs_widget_listing_loop' );

while( $query->have_posts() ) { $query->the_post();
    $job_details = get_awsm_job_details();
?>
<div class="awsm-list-item" id="awsm-list-item-<?php echo esc_attr__( $job_details['id'] ); ?>">
        <div class="awsm-job-item">
            <div class="awsm-list-left-col">
                <?php
                    /**
                     * before_awsm_jobs_listing_left_col_content hook
                     * 
                     * @since 1.1
                     */
                    do_action( 'before_awsm_jobs_listing_left_col_content' );
                ?>

                <h2 class="awsm-job-post-title">
                    <?php
                        echo apply_filters( 'awsm_jobs_listing_title', $job_details['title'], 'list' );
                    ?>
                </h2>

                <?php
                    /**
                     * after_awsm_jobs_listing_left_col_content hook
                     * 
                     * @since 1.1
                     */
                    do_action( 'after_awsm_jobs_listing_left_col_content' );
                ?>
            </div>

            <div class="awsm-list-right-col">
                <?php
                    /**
                     * before_awsm_jobs_listing_right_col_content hook
                     * 
                     * @since 1.1
                     */
                    do_action( 'before_awsm_jobs_listing_right_col_content' );

                    awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs );

                    awsm_job_more_details( $job_details['permalink'], 'list' );

                    /**
                     * after_awsm_jobs_listing_right_col_content hook
                     * 
                     * @since 1.1
                     */
                    do_action( 'after_awsm_jobs_listing_right_col_content' ); ?>
            </div>
        </div>
    </div>
<?php
   }

wp_reset_postdata();

/**
 * after_awsm_jobs_listing_loop hook
 * 
 * Fires after The Loop
 * 
 * @since 1.1
 */
do_action( 'after_awsm_recent_jobs_widget_listing_loop' );