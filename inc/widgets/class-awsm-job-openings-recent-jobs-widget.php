<?php
/**
 * AWSM: Recent Jobs Widget
 *
 * @package wp-job-openings
 * @since 1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Recent_Jobs_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'awsm_widget_recent_jobs',
			'description'                 => esc_html__( 'Your site&#8217;s most recent Job listings.', 'wp-job-openings' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'awsm-recent-jobs', esc_html__( 'AWSM: Recent Jobs', 'wp-job-openings' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$title            = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Jobs', 'wp-job-openings' );
		$title            = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$default_num_jobs = get_option( 'awsm_jobs_list_per_page' );
		$num_jobs         = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : $default_num_jobs;
		if ( ! $num_jobs ) {
			$num_jobs = $default_num_jobs;
		}
		$show_spec = isset( $instance['show_spec'] ) ? $instance['show_spec'] : true;
		$show_more = isset( $instance['show_more'] ) ? $instance['show_more'] : true;

		$query_args = apply_filters(
			'awsm_widget_recent_jobs_args',
			array(
				'post_type'           => 'awsm_job_openings',
				'posts_per_page'      => $num_jobs,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
			),
			$instance
		);

		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return;
		}

		if ( ! function_exists( 'get_awsm_jobs_template_path' ) ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		do_action( 'before_awsm_recent_jobs_widget_content', $args, $instance );

		include get_awsm_jobs_template_path( 'recent-jobs', 'widgets' );

		do_action( 'after_awsm_recent_jobs_widget_content', $args, $instance );

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$num_jobs  = isset( $instance['number'] ) ? $instance['number'] : get_option( 'awsm_jobs_list_per_page' );
		$show_spec = isset( $instance['show_spec'] ) ? (bool) $instance['show_spec'] : true;
		$show_more = isset( $instance['show_more'] ) ? (bool) $instance['show_more'] : true;
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-job-openings' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of jobs to show:', 'wp-job-openings' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo absint( $num_jobs ); ?>" size="3" /></p>

			<p><input class="checkbox" type="checkbox"<?php checked( $show_spec ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_spec' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_spec' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_spec' ) ); ?>"><?php esc_html_e( 'Display Job Specifications?', 'wp-job-openings' ); ?></label></p>

			<p><input class="checkbox" type="checkbox"<?php checked( $show_more ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_more' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_more' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_more' ) ); ?>"><?php esc_html_e( "Display 'More Details' link?", 'wp-job-openings' ); ?></label></p>
		<?php
		return 'form';
	}

	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['number']    = absint( $new_instance['number'] );
		$instance['show_spec'] = isset( $new_instance['show_spec'] ) ? (bool) $new_instance['show_spec'] : false;
		$instance['show_more'] = isset( $new_instance['show_more'] ) ? (bool) $new_instance['show_more'] : false;
		return $instance;
	}
}

register_widget( 'AWSM_Job_Openings_Recent_Jobs_Widget' );
