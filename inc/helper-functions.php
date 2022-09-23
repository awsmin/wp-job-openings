<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'awsm_jobs_sanitize_textarea' ) ) {
	function awsm_jobs_sanitize_textarea( $input ) {
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			$input = sanitize_textarea_field( $input );
		} else {
			$input = esc_textarea( $input );
		}
		return $input;
	}
}

if ( ! function_exists( 'get_awsm_jobs_date_format' ) ) {
	function get_awsm_jobs_date_format( $id = '', $format = '' ) {
		/**
		 * Filters the date format used in WP Job Openings.
		 *
		 * @since 2.1.0
		 *
		 * @param string $format The date format.
		 * @param string $id Unique ID to filter the date format.
		 */
		$format = apply_filters( 'awsm_jobs_date_format', $format, $id );
		if ( empty( $format ) ) {
			$format = get_option( 'date_format' );
		}
		return $format;
	}
}

if ( ! function_exists( 'get_awsm_jobs_time_format' ) ) {
	function get_awsm_jobs_time_format( $id = '', $format = '' ) {
		/**
		 * Filters the time format used in WP Job Openings.
		 *
		 * @since 2.1.0
		 *
		 * @param string $format The time format.
		 * @param string $id Unique ID to filter the time format.
		 */
		$format = apply_filters( 'awsm_jobs_time_format', $format, $id );
		if ( empty( $format ) ) {
			$format = get_option( 'time_format' );
		}
		return $format;
	}
}

if ( ! function_exists( 'awsm_jobs_is_valid_template_file' ) ) {
	function awsm_jobs_is_valid_template_file( $filename, $unsupported_versions = array() ) {
		$is_valid         = true;
		$template_content = @file_get_contents( $filename );
		if ( ! empty( $template_content ) ) {
			if ( strpos( $template_content, '@version' ) === false ) {
				$is_valid = false;
			}
			if ( ! empty( $unsupported_versions ) && is_array( $unsupported_versions ) ) {
				foreach ( $unsupported_versions as $unsupported_version ) {
					if ( strpos( $template_content, "@version {$unsupported_version}" ) !== false ) {
						$is_valid = false;
						break;
					}
				}
			}
		}
		return $is_valid;
	}
}

if ( ! function_exists( 'awsm_jobs_get_original_image_url' ) ) {
	function awsm_jobs_get_original_image_url( $attachment_id ) {
		$image_url = false;
		if ( function_exists( 'wp_get_original_image_url' ) ) {
			$image_url = wp_get_original_image_url( $attachment_id );
		} else {
			if ( wp_attachment_is_image( $attachment_id ) ) {
				$image_url = wp_get_attachment_url( $attachment_id );
			}
		}
		return $image_url;
	}
}

if ( ! function_exists( 'awsm_jobs_array_flatten' ) ) {
	function awsm_jobs_array_flatten( $array ) {
		$result = array();
		if ( is_array( $array ) ) {
			array_walk_recursive(
				$array,
				function ( $item ) use ( &$result ) {
					$result[] = $item;
				}
			);
		}
		return $result;
	}
}

if ( ! function_exists( 'awsm_jobs_is_akismet_active' ) ) {
	function awsm_jobs_is_akismet_active() {
		$is_active = false;
		if ( function_exists( 'akismet_get_key' ) ) {
			$akismet_key = akismet_get_key();
			return ! empty( $akismet_key );
		}
		return $is_active;
	}
}

if ( ! function_exists( 'awsm_jobs_wp_editor_settings' ) ) {
	function awsm_jobs_wp_editor_settings( $editor_id = '', $settings = array() ) {
		$default_settings = array(
			'media_buttons' => true,
			'editor_height' => 200,
			'textarea_rows' => 12,
			'editor_css'    => '<style>.wp-editor-tabs, .wp-editor-tabs * { box-sizing: content-box; -webkit-box-sizing: content-box; }</style>',
			'tinymce'       => array(
				'wpautop'     => true,
				'content_css' => AWSM_JOBS_PLUGIN_URL . '/assets/css/editor.min.css',
				'toolbar1'    => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,spellchecker,wp_adv',
				'toolbar2'    => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
			),
			'quicktags'     => array(
				'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close',
			),
		);
		$settings         = wp_parse_args( $settings, $default_settings );
		/**
		 * Filters the WP Editor settings.
		 *
		 * @since 3.3.0
		 *
		 * @param array $settings The WP Editor settings.
		 * @param string $editor_id The Editor ID.
		 */
		return apply_filters( 'awsm_jobs_wp_editor_settings', $settings, $editor_id );
	}
}

if ( ! function_exists( 'awsm_jobs_wp_editor' ) ) {
	function awsm_jobs_wp_editor( $content, $editor_id, $settings = array() ) {
		$settings = awsm_jobs_wp_editor_settings( $editor_id, $settings );
		wp_editor( wp_kses_post( $content ), $editor_id, $settings );
	}
}

if ( ! function_exists( 'awsm_jobs_mail_content_type' ) ) {
	function awsm_jobs_mail_content_type() {
		return 'text/html';
	}
}
