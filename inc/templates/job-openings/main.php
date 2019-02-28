<?php
/**
 * Main template part for job openings
 * 
 * Override this by copying it to currenttheme/wp-job-openings/job-openings/main.php
 * 
 * @package wp-job-openings
 * @version 1.3
 */

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

$view = awsm_jobs_view();
$awsm_filters = get_option( 'awsm_jobs_filter' );
$listing_specs = get_option( 'awsm_jobs_listing_specs' );
$shortcode_atts = isset( $shortcode_atts ) ? $shortcode_atts : array();

/**
 * before_awsm_jobs_listing_loop hook
 * 
 * Fires before The Loop to query for jobs
 * 
 * @since 1.1
 */
do_action( 'before_awsm_jobs_listing_loop' );

while( $query->have_posts() ) { $query->the_post();
    $job_details = get_awsm_job_details();
    $attrs = sprintf( 'class="awsm-%1$s-item" id="awsm-%1$s-item-%2$s"', $view, $job_details['id'] );

    echo ( $view === 'grid' ) ? sprintf( '<a href="%1$s" %2$s>', $job_details['permalink'], $attrs ) : '<div ' . $attrs . '>';
?>
        <div class="awsm-job-item">
            <div class="awsm-<?php echo $view; ?>-left-col">
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
                        $title = ( $view === 'grid' ) ? $job_details['title'] : sprintf( '<a href="%2$s">%1$s</a>', $job_details['title'], $job_details['permalink'] );
                        echo apply_filters( 'awsm_jobs_listing_title', $title, $view );
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

            <div class="awsm-<?php echo $view; ?>-right-col">
                <?php
                    /**
                     * before_awsm_jobs_listing_right_col_content hook
                     * 
                     * @since 1.1
                     */
                    do_action( 'before_awsm_jobs_listing_right_col_content' );

                    awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs );

                    awsm_job_more_details( $job_details['permalink'], $view );

                    /**
                     * after_awsm_jobs_listing_right_col_content hook
                     * 
                     * @since 1.1
                     */
                    do_action( 'after_awsm_jobs_listing_right_col_content' ); ?>
            </div>
        </div>
<?php
    echo ( $view === 'grid' ) ? '</a>' : '</div>';
}

wp_reset_postdata();

/**
 * after_awsm_jobs_listing_loop hook
 * 
 * Fires after The Loop
 * 
 * @since 1.1
 */
do_action( 'after_awsm_jobs_listing_loop' );

awsm_jobs_load_more( $query, $shortcode_atts );