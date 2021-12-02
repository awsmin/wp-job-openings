<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Overview {
	private static $instance = null;

	protected $cpath = null;

	public static $menu_slug = 'awsm-jobs-overview';

	public static $screen_id = 'awsm_job_openings_page_awsm-jobs-overview';

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 1 );
		add_action( 'admin_init', array( $this, 'redirect_to_overview' ) );
		add_action( 'add_meta_boxes_' . self::$screen_id, array( $this, 'register_overview_widgets' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_widget_path( $basename, $custom_id = false ) {
		$path      = $this->cpath . "/templates/overview/widgets/{$basename}.php";
		$unique_id = self::$menu_slug . '-' . $basename;
		if ( ! empty( $custom_id ) ) {
			$unique_id = $custom_id;
		}
		/**
		 * Filters the overview widget template path.
		 *
		 * @since 3.0.0
		 *
		 * @param string $path Template path.
		 * @param string $unique_id Unique ID to filter the path.
		 */
		return apply_filters( 'awsm_jobs_overview_widget_template_path', $path, $unique_id );
	}

	public function admin_menu() {
		$wp_version = get_bloginfo( 'version' );
		$page_title = esc_html__( 'WP Job Openings - Overview', 'wp-job-openings' );
		$menu_title = esc_html__( 'Overview', 'wp-job-openings' );
		if ( version_compare( $wp_version, '5.3', '>=' ) ) {
			add_submenu_page( 'edit.php?post_type=awsm_job_openings', $page_title, $menu_title, 'edit_jobs', self::$menu_slug, array( $this, 'overview_page' ), 0 );
		} else {
			add_submenu_page( 'edit.php?post_type=awsm_job_openings', $page_title, $menu_title, 'edit_jobs', self::$menu_slug, array( $this, 'overview_page' ) );
		}
	}

	public function overview_page() {
		include_once $this->cpath . '/templates/overview/main.php';
	}

	public function redirect_to_overview() {
		global $pagenow;
		if ( isset( $pagenow ) && $pagenow === 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === self::$menu_slug ) {
			wp_safe_redirect( add_query_arg( array( 'page' => self::$menu_slug ), admin_url( 'edit.php?post_type=awsm_job_openings' ) ) );
			exit;
		}
	}

	public function register_overview_widgets() {
		$widgets = array(
			'applications-analytics' => array(
				'active'   => current_user_can( 'edit_applications' ),
				'name'     => esc_html__( 'Applications Analytics', 'wp-job-openings' ),
				'priority' => 'high',
			),
			'get-started'            => array(
				'name'     => esc_html__( 'Get Started', 'wp-job-openings' ),
				'context'  => 'side',
				'priority' => 'high',
			),
			'applications-by-status' => array(
				'active'   => current_user_can( 'edit_applications' ) && ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ),
				'name'     => esc_html__( 'Applications by Status', 'wp-job-openings' ),
				'context'  => 'side',
				'callback' => array( $this, 'applications_by_status_widget' ),
			),
			'recent-applications'    => array(
				'active' => current_user_can( 'edit_others_applications' ),
				'name'   => esc_html__( 'Recent Applications', 'wp-job-openings' ),
			),
			'open-positions'         => array(
				'name'     => esc_html__( 'Open Positions', 'wp-job-openings' ),
				'context'  => 'side',
				'callback' => array( $this, 'open_positions_widget' ),
			),
			'your-listings'          => array(
				'name'     => esc_html__( 'Your Listings', 'wp-job-openings' ),
				'callback' => array( $this, 'your_listings_widget' ),
			),
		);
		/**
		 * Filters the overview widgets.
		 *
		 * @since 3.0.0
		 *
		 * @param array $widgets Overview widgets.
		 */
		$widgets = apply_filters( 'awsm_jobs_overview_widgets', $widgets );

		foreach ( $widgets as $widget_id => $widget_data ) {
			$defaults    = array(
				'active'   => true,
				'name'     => '',
				'context'  => 'normal',
				'priority' => 'default',
			);
			$widget_data = wp_parse_args( $widget_data, $defaults );
			if ( ! $widget_data['active'] ) {
				continue;
			}

			$callback = '';
			if ( isset( $widget_data['callback'] ) ) {
				$callback = $widget_data['callback'];
			} else {
				$callback = function () use ( $widget_id ) {
					include_once $this->get_widget_path( $widget_id );
				};
			}
			add_meta_box( self::$menu_slug . '-' . $widget_id, $widget_data['name'], $callback, self::$screen_id, $widget_data['context'], $widget_data['priority'] );
		}
	}

	public function applications_by_status_widget() {
		/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
		$pro_link = sprintf( esc_html__( 'This feature requires %1$sPRO Plan%2$s to work', 'wp-job-openings' ), '<a href="https://awsm.in/get/wpjo-pro/">', '</a>' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<div class="awsm-jobs-overview-widget-wrapper"><div class="awsm-jobs-pro-feature"><img src="%2$s"><p>%1$s</p></div></div>', $pro_link, esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/applications-by-status-chart.png' ) );
	}

	public function open_positions_widget() {
		$widget_id = 'open-positions';
		$jobs      = self::get_jobs(
			array(
				'numberjobs' => 10,
				'job_status' => 'publish',
			)
		);
		include $this->get_widget_path( 'job-listings', self::$menu_slug . '-' . $widget_id );
	}

	public function your_listings_widget() {
		$widget_id = 'your-listings';
		$jobs      = self::get_jobs_by_author();
		include $this->get_widget_path( 'job-listings', self::$menu_slug . '-' . $widget_id );
		self::get_applications_analytics_data();
	}

	public static function get_jobs( $args ) {
		global $wpdb;
		$defaults    = array(
			'numberjobs' => -1,
			'job_status' => array( 'publish', 'expired', 'future', 'draft', 'pending', 'private' ),
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$values = array();
		$where  = "WHERE {$wpdb->posts}.post_type = 'awsm_job_openings'";
		// status.
		if ( is_string( $parsed_args['job_status'] ) ) {
			$where   .= " AND {$wpdb->posts}.post_status = %s";
			$values[] = sanitize_text_field( $parsed_args['job_status'] );
		} elseif ( is_array( $parsed_args['job_status'] ) ) {
			$status             = array_map( 'sanitize_text_field', $parsed_args['job_status'] );
			$status_placeholder = rtrim( str_repeat( "{$wpdb->posts}.post_status = %s OR ", count( $status ) ), ' OR ' );
			$where             .= " AND ({$status_placeholder})";
			$values             = array_merge( $values, $status );
		}
		// author.
		if ( isset( $parsed_args['author_id'] ) ) {
			$where   .= " AND {$wpdb->posts}.post_author = %d";
			$values[] = $parsed_args['author_id'];
		}
		// limit.
		$limit = '';
		if ( $parsed_args['numberjobs'] !== -1 ) {
			$limit   .= ' LIMIT %d';
			$values[] = $parsed_args['numberjobs'];
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->posts}.ID, COUNT(applications.ID) AS applications_count FROM {$wpdb->posts} LEFT JOIN {$wpdb->posts} AS applications ON {$wpdb->posts}.ID = applications.post_parent AND applications.post_type = 'awsm_job_application' {$where} GROUP BY {$wpdb->posts}.ID ORDER BY applications_count DESC, {$wpdb->posts}.ID{$limit}", $values ), OBJECT );
		return $results;
	}

	public static function get_jobs_by_author( $numberjobs = 10 ) {
		$args = array(
			'numberjobs' => $numberjobs,
			'author_id'  => get_current_user_id(),
		);
		return self::get_jobs( $args );
	}

	public static function get_applications_analytics_data( $date_query = array(), $key_format = 'n', $label_format = 'M' ) {
		$analytics_data = array();
		if ( ! current_user_can( 'edit_applications' ) ) {
			return $analytics_data;
		}

		if ( empty( $date_query ) ) {
			$date_query = array(
				array(
					'year' => gmdate( 'Y' ),
				),
			);
		}
		$args         = array(
			'orderby'    => 'date',
			'order'      => 'ASC',
			'date_query' => $date_query,
		);
		$applications = AWSM_Job_Openings::get_all_applications( 'ids', $args );
		if ( ! empty( $applications ) ) {
			$data = array();
			foreach ( $applications as $application_id ) {
				$key                   = get_post_time( $key_format, false, $application_id );
				$label                 = get_post_time( $label_format, false, $application_id, true );
				$data[ $key ]['label'] = esc_html( $label );
				$count                 = 1;
				if ( isset( $data[ $key ]['count'] ) ) {
					$count = $data[ $key ]['count'];
					$count++;
				}
				$data[ $key ]['count'] = $count;
			}
			$analytics_data['labels'] = array_values( wp_list_pluck( $data, 'label' ) );
			$analytics_data['data']   = array_values( wp_list_pluck( $data, 'count' ) );
		}
		return $analytics_data;
	}
}
