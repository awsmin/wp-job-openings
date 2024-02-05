<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Core {
	private static $instance = null;

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ), 5 );

		// hide uploaded files.
		if ( get_option( 'awsm_hide_uploaded_files' ) === 'hide_files' ) {
			add_action( 'pre_get_posts', array( $this, 'list_attachments' ), 100 );
			add_filter( 'ajax_query_attachments_args', array( $this, 'grid_attachments' ), 100 );
		}

		add_filter( 'post_updated_messages', array( $this, 'job_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'jobs_bulk_updated_messages' ), 10, 2 );
		// Login redirect for HR user.
		add_filter( 'login_redirect', array( $this, 'login_redirect' ), 10, 3 );

		// WooCommerce - Allow the backend access for users with HR Role.
		add_filter( 'woocommerce_disable_admin_bar', array( $this, 'woocommerce_disable_backend_access' ) );
		add_filter( 'woocommerce_prevent_admin_access', array( $this, 'woocommerce_disable_backend_access' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		$this->unregister_awsm_job_openings_post_type();
		$this->register_post_types();
		$this->manage_default_roles_caps();
		$this->add_custom_role();
		$this->create_upload_directory();
	}

	public function unregister() {
		$this->unregister_awsm_job_openings_post_type();
		$this->manage_default_roles_caps( 'remove' );
		$this->remove_custom_role();
	}

	public function register_post_types() {

		if ( post_type_exists( 'awsm_job_openings' ) ) {
			return;
		}

		$labels = array(
			'name'                     => __( 'Job Openings', 'wp-job-openings' ),
			'singular_name'            => __( 'Job', 'wp-job-openings' ),
			'add_new'                  => __( 'New Opening', 'wp-job-openings' ),
			'add_new_item'             => __( 'Add New Job', 'wp-job-openings' ),
			'edit_item'                => __( 'Edit Job', 'wp-job-openings' ),
			'new_item'                 => __( 'New job', 'wp-job-openings' ),
			'search_items'             => __( 'Search Jobs', 'wp-job-openings' ),
			'not_found'                => __( 'No Jobs found', 'wp-job-openings' ),
			'not_found_in_trash'       => __( 'No Jobs found in Trash', 'wp-job-openings' ),
			'parent_item_colon'        => __( 'Parent Job :', 'wp-job-openings' ),
			'menu_name'                => __( 'Job Openings', 'wp-job-openings' ),
			'view_item'                => __( 'View Job listing', 'wp-job-openings' ),
			'view_items'               => __( 'View Job listings', 'wp-job-openings' ),
			'item_published'           => __( 'Job listing published.', 'wp-job-openings' ),
			'item_published_privately' => __( 'Job listing published privately.', 'wp-job-openings' ),
			'item_reverted_to_draft'   => __( 'Job listing reverted to draft.', 'wp-job-openings' ),
			'item_scheduled'           => __( 'Job listing scheduled.', 'wp-job-openings' ),
			'item_updated'             => __( 'Job listing updated.', 'wp-job-openings' ),
		);

		$has_archive            = get_option( 'awsm_jobs_disable_archive_page' ) !== 'disable' ? true : false;
		$with_front             = get_option( 'awsm_jobs_remove_permalink_front_base' ) !== 'remove' ? true : false;
		$supports               = array( 'title', 'editor', 'excerpt', 'author', 'custom-fields', 'publicize' );
		$featured_image_support = get_option( 'awsm_jobs_enable_featured_image' );
		if ( $featured_image_support === 'enable' ) {
			$supports[] = 'thumbnail';
		}

		/**
		 * Filters 'awsm_job_openings' post type arguments.
		 *
		 * @since 1.4
		 *
		 * @param array $args arguments.
		 */
		$args = apply_filters(
			'awsm_job_openings_args',
			array(
				'has_archive'     => $has_archive,
				'labels'          => $labels,
				'hierarchical'    => false,
				'map_meta_cap'    => true,
				'taxonomies'      => array(),
				'public'          => true,
				'show_ui'         => true,
				'show_in_rest'    => true,
				'show_in_menu'    => true,
				'rewrite'         => array(
					'slug'       => get_option( 'awsm_permalink_slug', 'jobs' ),
					'with_front' => $with_front,
				),
				'capability_type' => 'job',
				'menu_icon'       => esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/nav-icon.svg' ),
				'supports'        => $supports,
			)
		);

		register_post_type( 'awsm_job_openings', $args );

		if ( post_type_exists( 'awsm_job_application' ) ) {
			return;
		}

		$labels = array(
			'name'               => __( 'Applications', 'wp-job-openings' ),
			'singular_name'      => __( 'Application', 'wp-job-openings' ),
			'menu_name'          => __( 'Applications', 'wp-job-openings' ),
			'edit_item'          => __( 'Applications', 'wp-job-openings' ),
			'search_items'       => __( 'Search Applications', 'wp-job-openings' ),
			'not_found'          => __( 'No Applications found', 'wp-job-openings' ),
			'not_found_in_trash' => __( 'No Applications found in Trash', 'wp-job-openings' ),
		);

		/**
		 * Filters 'awsm_job_application' post type arguments.
		 *
		 * @since 1.4
		 *
		 * @param array $args arguments.
		 */
		$args = apply_filters(
			'awsm_job_application_args',
			array(
				'labels'          => $labels,
				'public'          => false,
				'show_ui'         => true,
				'map_meta_cap'    => true,
				'show_in_menu'    => 'edit.php?post_type=awsm_job_openings',
				'capability_type' => 'application',
				'capabilities'    => array(
					'create_posts' => 'do_not_allow',
				),
				'supports'        => false,
				'rewrite'         => false,
			)
		);

		register_post_type( 'awsm_job_application', $args );
	}

	private function get_caps() {
		$caps = array(
			'level_1' => array(
				'edit_jobs'           => true,
				'delete_jobs'         => true,
				'edit_applications'   => true,
				'delete_applications' => true,
			),
			'level_2' => array(
				'edit_published_jobs'           => true,
				'delete_published_jobs'         => true,
				'publish_jobs'                  => true,
				'edit_published_applications'   => true,
				'delete_published_applications' => true,
				'publish_applications'          => true,
			),
			'level_3' => array(
				'edit_others_jobs'            => true,
				'read_private_jobs'           => true,
				'delete_private_jobs'         => true,
				'delete_others_jobs'          => true,
				'edit_private_jobs'           => true,
				'edit_others_applications'    => true,
				'read_private_applications'   => true,
				'delete_private_applications' => true,
				'delete_others_applications'  => true,
				'edit_private_applications'   => true,
			),
			'level_4' => array(
				'manage_awsm_jobs' => true,
			),
		);
		return $caps;
	}

	private function manage_default_roles_caps( $action = 'add' ) {
		$caps      = $this->get_caps();
		$role_caps = array(
			'administrator' => array_merge( $caps['level_1'], $caps['level_2'], $caps['level_3'], $caps['level_4'] ),
			'editor'        => array_merge( $caps['level_1'], $caps['level_2'], $caps['level_3'] ),
			'author'        => array_merge( $caps['level_1'], $caps['level_2'] ),
			'contributor'   => $caps['level_1'],
		);
		foreach ( $role_caps as $slug => $current_caps ) {
			$role = get_role( $slug );
			if ( $role ) {
				foreach ( $current_caps as $current_cap => $value ) {
					if ( $action === 'remove' ) {
						$role->remove_cap( $current_cap );
					} else {
						$role->add_cap( $current_cap );
					}
				}
			}
		}
	}

	private function add_custom_role() {
		$caps                    = $this->get_caps();
		$hr_caps                 = array_merge( $caps['level_1'], $caps['level_2'], $caps['level_3'], $caps['level_4'] );
		$hr_caps['read']         = true;
		$hr_caps['upload_files'] = true;
		add_role( 'hr', __( 'HR', 'wp-job-openings' ), $hr_caps );
	}

	private function remove_custom_role() {
		if ( get_role( 'hr' ) ) {
			remove_role( 'hr' );
		}
	}

	public function unregister_awsm_job_openings_post_type() {
		global $wp_post_types;
		if ( isset( $wp_post_types['awsm_job_openings'] ) ) {
			unset( $wp_post_types['awsm_job_openings'] );
			return true;
		}
		return false;
	}

	private function create_upload_directory() {
		$upload_dir = wp_upload_dir();
		$base_dir   = trailingslashit( $upload_dir['basedir'] );
		$upload_dir = $base_dir . AWSM_JOBS_UPLOAD_DIR_NAME;
		$files      = array(
			array(
				'name'    => 'index.html',
				'content' => '',
			),
			array(
				'name'    => '.htaccess',
				'content' => 'Options -Indexes',
			),
		);
		if ( wp_mkdir_p( $upload_dir ) ) {
			foreach ( $files as $file ) {
				$current_file = trailingslashit( $upload_dir ) . $file['name'];
				if ( ! file_exists( $current_file ) ) {
					$handle = @fopen( $current_file, 'w' );
					if ( $handle ) {
						fwrite( $handle, $file['content'] );
						fclose( $handle );
					}
				}
			}
		}
	}

	public static function get_attachments_meta_query( $meta_query ) {
		$query = array(
			'relation' => 'OR',
			array(
				'key'     => '_wp_attached_file',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_wp_attached_file',
				'compare' => 'NOT LIKE',
				'value'   => AWSM_JOBS_UPLOAD_DIR_NAME,
			),
		);

		if ( is_array( $meta_query ) && ! empty( $meta_query ) ) {
			$meta_query[] = $query;
		} else {
			$meta_query = array( $query );
		}
		return $meta_query;
	}

	public function list_attachments( $query ) {
		if ( is_admin() && $query->is_main_query() ) {
			$screen = get_current_screen();
			if ( ! empty( $screen ) && $screen->id === 'upload' && $screen->post_type === 'attachment' ) {
				$meta_query = $query->get( 'meta_query' );
				$query->set( 'meta_query', self::get_attachments_meta_query( $meta_query ) );
			}
		}
	}

	public function grid_attachments( $query ) {
		if ( is_admin() ) {
			$meta_query          = isset( $query['meta_query'] ) ? $query['meta_query'] : array();
			$query['meta_query'] = self::get_attachments_meta_query( $meta_query );
		}
		return $query;
	}

	public function job_updated_messages( $messages ) {
		global $post, $post_ID;

		$permalink   = get_permalink( $post_ID );
		$preview_url = function_exists( 'get_preview_post_link' ) ? get_preview_post_link( $post ) : add_query_arg( 'preview', 'true', $permalink );

		// Preview job link.
		$preview_post_link_html = sprintf(
			' <a target="_blank" href="%1$s">%2$s</a>',
			esc_url( $preview_url ),
			__( 'Preview Job listing', 'wp-job-openings' )
		);
		// Scheduled job preview link.
		$scheduled_post_link_html = sprintf(
			' <a target="_blank" href="%1$s">%2$s</a>',
			esc_url( $permalink ),
			__( 'Preview Job listing', 'wp-job-openings' )
		);
		// View job link.
		$view_post_link_html = sprintf(
			' <a href="%1$s">%2$s</a>',
			esc_url( $permalink ),
			__( 'View Job listing', 'wp-job-openings' )
		);

		$scheduled_date = date_i18n( get_awsm_jobs_date_format( 'scheduled-date' ) . ' @ ' . get_awsm_jobs_time_format( 'scheduled-date' ), strtotime( $post->post_date ) );

		$messages['awsm_job_openings'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Job listing updated.', 'wp-job-openings' ) . $view_post_link_html,
			2  => __( 'Custom field updated.', 'default' ),
			3  => __( 'Custom field deleted.', 'default' ),
			4  => __( 'Job listing updated.', 'wp-job-openings' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Job listing restored to revision from %s.', 'wp-job-openings' ), wp_post_revision_title( intval( $_GET['revision'] ), false ) ) : false,
			6  => __( 'Job listing published.', 'wp-job-openings' ) . $view_post_link_html,
			7  => __( 'Job listing saved.', 'wp-job-openings' ),
			8  => __( 'Job listing submitted.', 'wp-job-openings' ) . $preview_post_link_html,
			/* translators: %s: scheduled date */
			9  => sprintf( __( 'Job listing scheduled for: %s.', 'wp-job-openings' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
			10 => __( 'Job listing draft updated.', 'wp-job-openings' ) . $preview_post_link_html,
		);

		$messages['awsm_job_application'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Application updated.', 'wp-job-openings' ),
			2  => __( 'Custom field updated.', 'default' ),
			3  => __( 'Custom field deleted.', 'default' ),
			4  => __( 'Application updated.', 'wp-job-openings' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Application restored to revision from %s.', 'wp-job-openings' ), wp_post_revision_title( intval( $_GET['revision'] ), false ) ) : false,
			6  => __( 'Application published.', 'wp-job-openings' ),
			7  => __( 'Application saved.', 'wp-job-openings' ),
			8  => __( 'Application submitted.', 'wp-job-openings' ),
			/* translators: %s: scheduled date */
			9  => sprintf( __( 'Application scheduled for: %s.', 'wp-job-openings' ), '<strong>' . $scheduled_date . '</strong>' ),
			10 => __( 'Application draft updated.', 'wp-job-openings' ),
		);
		return $messages;
	}

	public function jobs_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['awsm_job_openings'] = array(
			/* translators: %s: job count */
			'updated'   => _n( '%s job listing updated.', '%s job listings updated.', $bulk_counts['updated'], 'wp-job-openings' ),
			/* translators: %s: job count */
			'locked'    => _n( '%s job listing not updated, somebody is editing it.', '%s job listings not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-job-openings' ),
			/* translators: %s: job count */
			'deleted'   => _n( '%s job listing permanently deleted.', '%s job listings permanently deleted.', $bulk_counts['deleted'], 'wp-job-openings' ),
			/* translators: %s: job count */
			'trashed'   => _n( '%s job listing moved to the Trash.', '%s job listings moved to the Trash.', $bulk_counts['trashed'], 'wp-job-openings' ),
			/* translators: %s: job count */
			'untrashed' => _n( '%s job listing restored from the Trash.', '%s job listings restored from the Trash.', $bulk_counts['untrashed'], 'wp-job-openings' ),
		);

		$bulk_messages['awsm_job_application'] = array(
			/* translators: %s: job application count */
			'updated'   => _n( '%s application updated.', '%s applications updated.', $bulk_counts['updated'], 'wp-job-openings' ),
			/* translators: %s: job application count */
			'locked'    => _n( '%s application not updated, somebody is editing it.', '%s applications not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-job-openings' ),
			/* translators: %s: job application count */
			'deleted'   => _n( '%s application permanently deleted.', '%s applications permanently deleted.', $bulk_counts['deleted'], 'wp-job-openings' ),
			/* translators: %s: job application count */
			'trashed'   => _n( '%s application moved to the Trash.', '%s applications moved to the Trash.', $bulk_counts['trashed'], 'wp-job-openings' ),
			/* translators: %s: job application count */
			'untrashed' => _n( '%s application restored from the Trash.', '%s applications restored from the Trash.', $bulk_counts['untrashed'], 'wp-job-openings' ),
		);
		return $bulk_messages;
	}

	/**
	 * Redirect users with HR Role to job page instead of profile page after login.
	 *
	 * @param string $redirect_to The redirect destination URL.
	 * @param string $requested_redirect_to The requested redirect destination URL.
	 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string
	 */
	public function login_redirect( $redirect_to, $requested_redirect_to, $user ) {
		if ( ! is_wp_error( $user ) && ( empty( $redirect_to ) || 'wp-admin/' === $redirect_to || admin_url() === $redirect_to ) ) {
			if ( ! empty( $user->roles ) && is_array( $user->roles ) && in_array( 'hr', $user->roles ) && ! $user->has_cap( 'edit_posts' ) && $user->has_cap( 'edit_jobs' ) ) {
				$url = add_query_arg( array( 'page' => 'awsm-jobs-overview' ), admin_url( 'edit.php?post_type=awsm_job_openings' ) );
				/**
				 * Filters login redirection URL for the HR user.
				 *
				 * @since 2.1.1
				 *
				 * @param string $redirect_url The redirect destination URL.
				 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise.
				 */
				$redirect_url = apply_filters( 'awsm_jobs_login_redirect', esc_url_raw( $url ), $user );
				if ( ! empty( $redirect_url ) ) {
					$redirect_to = $redirect_url;
				}
			}
		}
		return $redirect_to;
	}

	public function woocommerce_disable_backend_access( $disable ) {
		if ( current_user_can( 'edit_jobs' ) ) {
			$disable = false;
		}
		return $disable;
	}
}
