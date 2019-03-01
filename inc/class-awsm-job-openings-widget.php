<?php

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWSM_Job_Openings_Widget extends WP_Widget {
	public function __construct(  ) {
        $widget_ops = array(
			'classname' => 'awsm_widget_recent_jobs',
			'description' => esc_html__( 'Your site’s most recent Job listings. ', 'wp-job-openings' ),
			'customize_selective_refresh' => true,
		);
        parent::__construct( 'awsm-recent-jobs', esc_html__( 'AWSM: Recent Jobs', 'wp-job-openings' ), $widget_ops );
    }

    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Jobs', 'wp-job-openings' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : get_option( 'awsm_jobs_list_per_page' );
		$query = new WP_Query( array(
            'post_type'           => 'awsm_job_openings',
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		) );

		if ( ! $query->have_posts() ) {
			return;
		}
		echo $args['before_widget'];
	
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>
		<div class="awsm-job-wrap">
			<div <?php awsm_jobs_view_class(); awsm_jobs_data_attrs(); ?>>
				<?php include get_awsm_jobs_template_path( 'main', 'job-openings' ); ?>
			</div>
		</div>
		<?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : get_option( 'awsm_jobs_list_per_page' );
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-job-openings' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of jobs to show:', 'wp-job-openings' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
       	$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];	
		return $instance;
    }
}