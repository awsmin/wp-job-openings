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
		$page_title = esc_html__( 'HireZoot - Overview', 'wp-job-openings' );
		$menu_title = esc_html__( 'Overview', 'wp-job-openings' );
		if ( version_compare( $wp_version, '5.3', '>=' ) ) {
			add_submenu_page( 'edit.php?post_type=awsm_job_openings', $page_title, $menu_title, 'edit_jobs', self::$menu_slug, array( $this, 'overview_page' ), 0 );
		} else {
			add_submenu_page( 'edit.php?post_type=awsm_job_openings', $page_title, $menu_title, 'edit_jobs', self::$menu_slug, array( $this, 'overview_page' ) );
		}
	}

	public function overview_page() {
		$jobs = self::get_jobs(
			array(
				'numberjobs'      => 7,
				'job_status'      => 'publish',
				'exclude_expired' => true,
				'orderby'         => 'recent_activity',
			)
		);
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
				'numberjobs'      => 10,
				'job_status'      => 'publish',
				'exclude_expired' => true,
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
			'orderby'    => 'post_date',
		);
		$parsed_args = wp_parse_args( $args, $defaults );
		/**
		 * Filters the arguments to retrieve jobs in the overview section.
		 *
		 * @since 3.3.3
		 *
		 * @param array $parsed_args Arguments to retrieve jobs.
		 * @param array $defaults Overview jobs arguments.
		 */
		$parsed_args = apply_filters( 'awsm_overview_jobs_args', $parsed_args, $defaults );

		$values = array();

		// Build application join respecting user capabilities.
		if ( current_user_can( 'edit_others_applications' ) ) {
			// Count all non-trashed applications.
			$join = "LEFT JOIN {$wpdb->posts} AS applications ON {$wpdb->posts}.ID = applications.post_parent AND applications.post_type = 'awsm_job_application' AND applications.post_status != 'trash'";
		} elseif ( current_user_can( 'edit_applications' ) ) {
			// Count applications only for jobs authored by the current user.
			$current_user_id = (int) get_current_user_id();
			$join            = "LEFT JOIN {$wpdb->posts} AS applications ON {$wpdb->posts}.ID = applications.post_parent AND applications.post_type = 'awsm_job_application' AND applications.post_status != 'trash' AND {$wpdb->posts}.post_author = {$current_user_id}";
		} else {
			// No application access — always count as 0.
			$join = "LEFT JOIN {$wpdb->posts} AS applications ON 1=0";
		}

		$where = 'WHERE 1=1';
		if ( isset( $parsed_args['tax_query'] ) && is_array( $parsed_args['tax_query'] ) ) {
			$in       = array();
			$term_ids = array();
			foreach ( $parsed_args['tax_query'] as $tax_terms ) {
				foreach ( $tax_terms['terms'] as $term_id ) {
					$in[]       = '%d';
					$term_ids[] = intval( $term_id );
				}
			}
			$in             = implode( ',', $in );
			$term_tax_query = "SELECT t.term_id, tt.term_taxonomy_id, tt.taxonomy FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE t.term_id IN ({$in})";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$term_tax_results = $wpdb->get_results( $wpdb->prepare( $term_tax_query, $term_ids ), ARRAY_A );
			if ( ! empty( $term_tax_results ) ) {
				$taxonomies_ids = array();
				foreach ( $term_tax_results as $term_tax_result ) {
					$taxonomy = $term_tax_result['taxonomy'];
					if ( ! isset( $taxonomies_ids[ $taxonomy ] ) ) {
						$taxonomies_ids[ $taxonomy ] = array();
					}
					$taxonomies_ids[ $taxonomy ][] = $term_tax_result['term_taxonomy_id'];
				}

				$index = 1;
				foreach ( $taxonomies_ids as $term_tax_ids ) {
					$join .= " LEFT JOIN {$wpdb->term_relationships} AS tt{$index} ON ({$wpdb->posts}.ID = tt{$index}.object_id)";
					$in    = array();
					foreach ( $term_tax_ids as $term_tax_id ) {
						$in[]     = '%d';
						$values[] = intval( $term_tax_id );
					}
					$in     = implode( ',', $in );
					$where .= " AND tt{$index}.term_taxonomy_id IN({$in})";
					++$index;
				}
			}
		}
		$where .= " AND {$wpdb->posts}.post_type = 'awsm_job_openings'";
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
		// exclude jobs whose expiry date meta has already passed.
		if ( ! empty( $parsed_args['exclude_expired'] ) ) {
			$join    .= " LEFT JOIN {$wpdb->postmeta} AS job_expiry ON {$wpdb->posts}.ID = job_expiry.post_id AND job_expiry.meta_key = 'awsm_job_expiry'";
			$where   .= " AND (job_expiry.meta_value IS NULL OR job_expiry.meta_value = '' OR job_expiry.meta_value >= %s)";
			$values[] = current_time( 'Y-m-d' );
		}
		// Order by the most recent application activity, falling back to the job's own publish date.
		if ( 'recent_activity' === $parsed_args['orderby'] ) {
			$orderby = "COALESCE(MAX(applications.post_date), {$wpdb->posts}.post_date) DESC";
		} else {
			$orderby = "{$wpdb->posts}.post_date DESC";
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->posts}.ID, COUNT(DISTINCT applications.ID) AS applications_count FROM {$wpdb->posts} {$join} {$where} GROUP BY {$wpdb->posts}.ID ORDER BY {$orderby}", $values ), OBJECT );

		// Remove jobs whose application limit has been exceeded (pro pack hooks awsm_jobs_active_count_ids).
		if ( ! empty( $results ) ) {
			$result_ids = array_map( 'intval', wp_list_pluck( $results, 'ID' ) );
			$active_ids = array_map( 'intval', (array) apply_filters( 'awsm_jobs_active_count_ids', $result_ids ) );
			if ( count( $active_ids ) !== count( $result_ids ) ) {
				$results = array_values(
					array_filter(
						$results,
						function ( $job ) use ( $active_ids ) {
							return in_array( intval( $job->ID ), $active_ids, true );
						}
					)
				);
			}
		}

		// Apply numberjobs limit in PHP so it comes after the active-count filter above.
		if ( $parsed_args['numberjobs'] !== -1 ) {
			$results = array_slice( $results, 0, (int) $parsed_args['numberjobs'] );
		}

		/**
		 * Filters the overview jobs result.
		 *
		 * @since 3.3.3
		 *
		 * @param array $results Overview jobs results.
		 * @param array $parsed_args Arguments to retrieve jobs.
		 */
		return apply_filters( 'awsm_overview_jobs', $results, $parsed_args );
	}

	public static function get_jobs_by_author( $numberjobs = 7 ) {
		$args = array(
			'numberjobs' => $numberjobs,
			'author_id'  => get_current_user_id(),
			'job_status' => 'publish',
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
					++$count;
				}
				$data[ $key ]['count'] = $count;
			}
			$analytics_data['labels'] = array_values( wp_list_pluck( $data, 'label' ) );
			$analytics_data['data']   = array_values( wp_list_pluck( $data, 'count' ) );
		}
		return $analytics_data;
	}
}
