<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Meta {
	private static $instance = null;

	protected $cpath = null;

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );
		add_action( 'add_meta_boxes', array( $this, 'awsm_register_meta_boxes' ) );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
		if ( isset( $_GET['awsm_action'] ) ) {
			if ( $_GET['awsm_action'] === 'download_resume' ) {
				add_action( 'plugins_loaded', array( $this, 'download_resume_handle' ) );
			} elseif ( $_GET['awsm_action'] === 'download_file' ) {
				add_action( 'plugins_loaded', array( $this, 'download_file_handle' ) );
			}
		}
		add_action( 'awsm_job_applicant_profile_details_resume_preview', array( $this, 'docs_viewer_handle' ) );
		add_filter( 'post_row_actions', array( $this, 'awsm_job_application_row_actions_label' ), 10, 2 );
		add_filter( 'wp_untrash_post_status', array( $this, 'awsm_job_application_restore_post_to_previous_status' ), 10, 3 );
		add_filter( 'post_class', array( $this, 'awsm_add_unread_application_class' ), 10, 3 );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function awsm_register_meta_boxes() {
		global $action, $post;

		if ( $action === 'edit' ) {
			add_meta_box( 'awsm-status-meta', esc_html__( 'Job Status', 'wp-job-openings' ), array( $this, 'awsm_job_status' ), 'awsm_job_openings', 'side', 'high' );
			add_meta_box( 'awsm-status-meta-applicant', esc_html__( 'Job Details', 'wp-job-openings' ), array( $this, 'awsm_job_status' ), 'awsm_job_application', 'side', 'low' );
		}

		$awsm_filters = get_option( 'awsm_jobs_filter' );
		if ( ! empty( $awsm_filters ) ) {
			add_meta_box( 'awsm-job-meta', esc_html__( 'Job Specifications', 'wp-job-openings' ), array( $this, 'awsm_job_handle' ), 'awsm_job_openings', 'normal', 'high' );
		}

		add_meta_box( 'awsm-expiry-meta', esc_html__( 'Job Expiry', 'wp-job-openings' ), array( $this, 'awsm_job_expiration' ), 'awsm_job_openings', 'side', 'low' );

		add_meta_box( 'awsm-job-details-meta', esc_html__( 'Applicant Details', 'wp-job-openings' ), array( $this, 'awsm_job_application_handle' ), 'awsm_job_application', 'normal', 'high' );

		if ( isset( $post ) && $post->post_type === 'awsm_job_application' ) {
			$awsm_attachment_id = get_post_meta( $post->ID, 'awsm_attachment_id', true );
			if ( ! empty( $awsm_attachment_id ) ) {
				$attachment_post = get_post( $awsm_attachment_id );
				if ( $attachment_post && $attachment_post->post_type === 'attachment' ) {
					add_meta_box( 'awsm-job-resume-preview', esc_html__( 'Resume Preview', 'wp-job-openings' ), array( $this, 'awsm_job_application_attachment_preview' ), 'awsm_job_application', 'normal', 'high' );
				}
			}
		}

		if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
			add_meta_box( 'awsm-application-actions-meta', esc_html__( 'Actions', 'wp-job-openings' ), array( $this, 'application_actions_meta_handler' ), 'awsm_job_application', 'side', 'high' );
			add_meta_box( 'awsm-get-the-pro-pack-meta', esc_html__( 'Upgrade to WPJO Pro', 'wp-job-openings' ), array( $this, 'get_pro_meta_handler' ), 'awsm_job_application', 'side', 'low' );
		}
	}

	public function awsm_job_status( $post ) {
		include $this->cpath . '/templates/meta/job-status.php';
	}

	public function awsm_job_handle( $post ) {
		include $this->cpath . '/templates/meta/job-specifications.php';
	}

	public function awsm_job_expiration( $post ) {
		include $this->cpath . '/templates/meta/job-expiry.php';
	}

	public function awsm_job_application_handle( $post ) {
		include $this->cpath . '/templates/meta/applicant-single.php';
	}

	public function is_main_applicant_viewed( $post_id ) {
		$is_viewed = get_post_meta( $post_id, 'awsm_application_viewed', true ) === '1';
		if ( ! $is_viewed ) {
			update_post_meta( $post_id, 'awsm_application_viewed', '1' );
		}
		return $is_viewed;
	}

	public function awsm_job_application_attachment_preview( $post ) {
		$awsm_attachment_id = get_post_meta( $post->ID, 'awsm_attachment_id', true );
		if ( ! empty( $awsm_attachment_id ) && is_string( $awsm_attachment_id ) && strlen( $awsm_attachment_id ) > 0 ) {
			include $this->cpath . '/templates/meta/resume-preview.php';
		}
	}

	public function application_actions_meta_handler( $post ) {
		include $this->cpath . '/templates/meta/application-actions.php';
	}

	public function get_pro_meta_handler() {
		include $this->cpath . '/templates/meta/get-pro.php';
	}

	public function remove_meta_boxes() {
		remove_meta_box( 'slugdiv', 'awsm_job_application', 'normal' );
		remove_meta_box( 'submitdiv', 'awsm_job_application', 'side' );
	}

	public static function set_applicant_single_view_tab() {
		$tab_list = array(
			'profile' => esc_html__( 'Profile', 'wp-job-openings' ),
		);
		return apply_filters( 'awsm_jobs_opening_applicant_single_tab_list', $tab_list );
	}

	public static function get_applicant_single_view_content( $post_id, $attachment_id ) {
		$tab_content = array();
		return apply_filters( 'awsm_jobs_opening_applicant_single_view_content', $tab_content, $post_id );
	}

	public function get_applicant_meta_details_list( $post_id, $preset_values = array() ) {
		$list         = '';
		$name         = '';
		$email        = '';
		$meta_details = array();

		$applicant_meta = apply_filters(
			'awsm_jobs_applicant_meta',
			array(
				'awsm_applicant_name'   => array(
					'label' => __( 'Name', 'wp-job-openings' ),
				),
				'awsm_applicant_email'  => array(
					'label' => __( 'Email Address', 'wp-job-openings' ),
				),
				'awsm_applicant_phone'  => array(
					'label' => __( 'Phone Number', 'wp-job-openings' ),
				),
				'awsm_applicant_letter' => array(
					'label'      => __( 'Bio', 'wp-job-openings' ),
					'multi-line' => true,
				),

			),
			$post_id
		);

		if ( ! empty( $applicant_meta ) && is_array( $applicant_meta ) ) {
			foreach ( $applicant_meta as $meta_key => $meta_options ) {
				$visible    = isset( $meta_options['visible'] ) ? $meta_options['visible'] : true;
				$multi_line = isset( $meta_options['multi-line'] ) ? $meta_options['multi-line'] : false;

				if ( $visible ) {
					$label = isset( $meta_options['label'] ) ? $meta_options['label'] : '';
					$value = '';

					if ( ! empty( $preset_values ) && isset( $preset_values[ $meta_key ] ) ) {
						$value = $preset_values[ $meta_key ];
					} elseif ( isset( $meta_options['value'] ) ) {
						$value = $meta_options['value'];
					} else {
						$value = get_post_meta( $post_id, $meta_key, true );
					}

					// Skip if the value is empty
					if ( empty( $value ) ) {
						continue;
					}

					// Separate the name
					if ( $meta_key === 'awsm_applicant_name' ) {
						$name = $value;
						continue; // Skip adding the name to the list
					} elseif ( $meta_key === 'awsm_applicant_email' ) {
						$email = $value;
					}

					$meta_content = '';
					if ( isset( $meta_options['type'] ) && ! empty( $value ) ) {
						if ( $meta_options['type'] === 'file' ) {
							$meta_content = sprintf(
								'<a href="%2$s" rel="nofollow"><strong>%1$s</strong></a>',
								esc_html__( 'Download File', 'wp-job-openings' ),
								$this->get_attached_file_download_url( $value, 'file', $label )
							);
						} elseif ( $meta_options['type'] === 'url' ) {
							$meta_content = sprintf(
								'<a href="%s" target="_blank" rel="nofollow">%s</a>',
								esc_url( $value ),
								esc_html( $value )
							);
						}
					} else {
						$meta_content = empty( $multi_line ) ? esc_html( $value ) : wp_kses(
							wpautop( $value ),
							array(
								'p'  => array(),
								'br' => array(),
							)
						);
					}

					$meta_content = apply_filters( 'awsm_jobs_applicant_meta_content', $meta_content, $meta_key, $applicant_meta, $post_id );
					if ( ! empty( $meta_content ) || is_numeric( $meta_content ) ) {
						$is_meta_group = isset( $meta_options['group'] ) ? $meta_options['group'] : false;
						$meta_content  = ! $is_meta_group ? '<span>' . $meta_content . '</span>' : $meta_content;
						$list         .= sprintf( '<li><label>%1$s</label>%2$s</li>', esc_html( $label ), '<div class="' . $meta_key . '">' . $meta_content . '</div>' );
					}

					// Add to meta details array
					$meta_details[ $meta_key ] = $value;
				}
			}
		}

		$list = apply_filters( 'awsm_jobs_applicant_meta_details_list', $list, $applicant_meta, $post_id );

		return array(
			'name'         => $name,
			'email'        => $email,
			'list'         => $list,
			'meta_details' => $meta_details,
		);
	}

	public function get_attached_file_details( $attachment_id ) {
		$details         = array();
		$attachment_file = get_attached_file( $attachment_id );
		if ( ! empty( $attachment_file ) ) {
			$file_type    = wp_check_filetype( $attachment_file );
			$file_size    = filesize( $attachment_file );
			$display_size = size_format( $file_size, 2 );
			$details      = array(
				'file_name' => $attachment_file,
				'file_type' => $file_type,
				'file_size' => array(
					'size'    => $file_size,
					'display' => $display_size,
				),
			);
		}
		return $details;
	}

	public function get_attached_file_download_url( $attachment_id, $type = 'resume', $label = '' ) {
		$query_vars = array(
			'awsm_id'     => $attachment_id,
			'awsm_nonce'  => wp_create_nonce( 'awsm_' . $type . '_download' ),
			'awsm_action' => 'download_' . $type,
		);
		if ( ! empty( $label ) ) {
			$query_vars['attachment_label'] = sanitize_title( $label );
		}
		$download_url = add_query_arg( $query_vars, get_edit_post_link() );
		return esc_url( $download_url );
	}

	public function attached_file_download_handler( $type, $suffix ) {
		if ( current_user_can( 'edit_others_applications' ) && isset( $_GET['awsm_id'] ) && isset( $_GET['awsm_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['awsm_nonce'], 'awsm_' . $type . '_download' ) ) {
				wp_die( esc_html__( 'Error occurred!', 'wp-job-openings' ) );
			}
			$attachment_id = intval( $_GET['awsm_id'] );
			if ( ! $attachment_id ) {
				wp_die( esc_html__( 'Invalid id.', 'wp-job-openings' ) );
			}
			$file_details = $this->get_attached_file_details( $attachment_id );
			if ( ! empty( $file_details ) ) {
				$file_name = sanitize_title( get_the_title( $attachment_id ) . $suffix );
				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: ' . $file_details['file_type']['type'] );
				header( 'Content-Disposition: attachment; filename="' . $file_name . '.' . $file_details['file_type']['ext'] . '"' );
				header( 'Expires: 0' );
				header( 'Pragma: no-cache' );
				if ( ! empty( $file_details['file_size']['size'] ) ) {
					header( 'Content-Length: ' . $file_details['file_size']['size'] );
				}
				readfile( $file_details['file_name'] );
				exit;
			} else {
				wp_die( esc_html__( 'File not found!', 'wp-job-openings' ) );
			}
		}
	}

	public function download_resume_handle() {
		$this->attached_file_download_handler( 'resume', '_' . __( 'resume', 'wp-job-openings' ) );
	}

	public function download_file_handle() {
		$suffix = isset( $_GET['attachment_label'] ) ? '-' . $_GET['attachment_label'] : '';
		$this->attached_file_download_handler( 'file', $suffix );
	}

	public function application_delete_action( $application_id ) {
		?>
			<div id="delete-action">
				<?php
				if ( current_user_can( 'delete_post', $application_id ) ) {
					if ( ! EMPTY_TRASH_DAYS ) {
						$delete_text = __( 'Delete Permanently', 'default' );
					} else {
						$delete_text = __( 'Move to Trash', 'default' );
					}
					printf( '<a class="submitdelete deletion" href="%2$s">%1$s</a>', esc_html( $delete_text ), esc_url( get_delete_post_link( $application_id ) ) );
				}
				?>
			</div>
		<?php
	}
	public function awsm_job_application_row_actions_label( $actions, $post ) {
		if ( $post->post_type === 'awsm_job_application' ) {
			if ( isset( $actions['edit'] ) ) {
				$actions['edit'] = str_replace( __( 'Edit', 'wp-job-openings' ), __( 'View', 'wp-job-openings' ), $actions['edit'] );
			}
		}
		return $actions;
	}

	public function awsm_job_application_restore_post_to_previous_status( $new_status, $post_id, $previous_status ) {
		$post = get_post( $post_id );

		if ( $post && $post->post_type === 'awsm_job_application' ) {
			if ( ! empty( $previous_status ) && $previous_status !== 'trash' ) {
				return $previous_status;
			}

			return 'publish';
		}

		return $new_status;
	}

	public function awsm_add_unread_application_class( $classes, $class, $post_id ) {
		if ( get_post_type( $post_id ) === 'awsm_job_application' ) {
			$post_status = get_post_status( $post_id );

			if ( $post_status === 'publish' ) {
				$is_viewed = get_post_meta( $post_id, 'awsm_application_viewed', true ) === '1';

				if ( ! $is_viewed ) {
					$classes[] = 'awsm-new-job';
				}
			}
		}

		return $classes;
	}

}
