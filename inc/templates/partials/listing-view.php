<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $awsm_filters = get_option( 'awsm_jobs_filter' );
    $listing_specs = get_option( 'awsm_jobs_listing_specs' );
    $view = $this->get_job_listing_view();

    while( $query->have_posts() ) { $query->the_post();
        $job_id = get_the_ID();
        $job_title = esc_html( get_the_title() );
        $permalink = esc_url( get_permalink() );
        $job_list_attrs = sprintf( 'class="awsm-%1$s-item" id="awsm-%1$s-item-%2$s"', $view, $job_id );

        echo ( $view === 'grid' ) ? sprintf( '<a href="%1$s" %2$s>', $permalink, $job_list_attrs ) : '<div ' . $job_list_attrs . '>';
?>
            <div class="awsm-job-item">
                <div class="awsm-<?php echo $view; ?>-left-col">
                    <h2 class="awsm-job-post-title">
                        <?php echo ( $view === 'grid' ) ? $job_title : sprintf( '<a href="%2$s">%1$s</a>', $job_title, $permalink ); ?>
                    </h2>
                </div>

                <div class="awsm-<?php echo $view; ?>-right-col">
                    <?php echo $this->get_specifications_content( $job_id, false, $awsm_filters, $listing_specs ); ?>

                    <div class="awsm-job-more-container">
                        <?php printf( '<%1$s class="awsm-job-more"%3$s>%2$s</%1$s> ', ( $view === 'grid' ) ? 'span' : 'a', esc_html__( 'More Details', 'wp-job-openings' ), ( $view === 'grid' ) ? '' : ' href="' . $permalink . '"' ); ?>
                        <span>&rarr;</span>
                    </div>
                </div>
            </div>
<?php
        echo ( $view === 'grid' ) ? '</a>' : '</div>';
    }

    wp_reset_postdata();

    $max_num_pages = $query->max_num_pages;
    $paged = ( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1;
    if( $max_num_pages > 1 && $paged < $max_num_pages ) :
?>
        <div class="awsm-load-more-main">
            <a href="#" class="awsm-load-more awsm-load-more-btn" data-page="<?php echo $paged; ?>"><?php _e( 'Load more...', 'wp-job-openings' ); ?></a>
        </div>
<?php endif; ?>