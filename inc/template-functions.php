<?php
/**
 * Template specific functions
 *
 * @package wp-job-openings
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_awsm_jobs_template_path' ) ) {
	function get_awsm_jobs_template_path( $slug, $dir_name = false ) {
		return AWSM_Job_Openings::get_template_path( "{$slug}.php", $dir_name );
	}
}

if ( ! function_exists( 'awsm_jobs_get_header' ) ) {
	function awsm_jobs_get_header() {
		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			require get_awsm_jobs_template_path( 'header', 'theme-compat' );
		} else {
			get_header();
		}
	}
}

if ( ! function_exists( 'awsm_jobs_get_footer' ) ) {
	function awsm_jobs_get_footer() {
		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			require get_awsm_jobs_template_path( 'footer', 'theme-compat' );
		} else {
			get_footer();
		}
	}
}

if ( ! function_exists( 'awsm_jobs_query' ) ) {
	function awsm_jobs_query( $shortcode_atts = array() ) {
		$args  = AWSM_Job_Openings::awsm_job_query_args( array(), $shortcode_atts );
		$query = new WP_Query( $args );
		return $query;
	}
}

if ( ! function_exists( 'awsm_jobs_view' ) ) {
	function awsm_jobs_view( $shortcode_atts = array() ) {
		return AWSM_Job_Openings::get_job_listing_view( $shortcode_atts );
	}
}

if ( ! function_exists( 'awsm_jobs_wrapper_class' ) ) {
	function awsm_jobs_wrapper_class( $echo = true ) {
		$wrapper_class = '';
		$form_style    = get_option( 'awsm_jobs_form_style', 'theme' );
		if ( $form_style === 'plugin' ) {
			$wrapper_class = ' awsm-job-form-plugin-style';
		}
		/**
		 * Filters the wrapper element class.
		 *
		 * @since 3.1.0
		 *
		 * @param string $wrapper_class Class names.
		 */
		$wrapper_class = apply_filters( 'awsm_jobs_wrapper_class', $wrapper_class );
		if ( $echo ) {
			echo esc_attr( $wrapper_class );
		} else {
			return $wrapper_class;
		}
	}
}

if ( ! function_exists( 'awsm_jobs_view_class' ) ) {
	function awsm_jobs_view_class( $class = '', $shortcode_atts = array() ) {
		$view_class = AWSM_Job_Openings::get_job_listing_view_class( $shortcode_atts );
		if ( ! empty( $class ) ) {
			$view_class = trim( $view_class . ' ' . $class );
		}
		printf( 'class="%s"', esc_attr( $view_class ) );
	}
}

if ( ! function_exists( 'awsm_jobs_listing_item_class' ) ) {
	function awsm_jobs_listing_item_class( $class = array() ) {
		$job_id  = get_the_ID();
		$classes = array( 'awsm-job-listing-item' );
		if ( is_awsm_job_expired() ) {
			$classes[] = 'awsm-job-expired-item';
		}
		if ( ! empty( $class ) ) {
			$classes = array_merge( $classes, $class );
		}
		/**
		 * Filters the classes for each job listing item.
		 *
		 * @since 2.1.0
		 *
		 * @param array $classes Array of class names.
		 * @param int $job_id The Job ID.
		 */
		$classes = apply_filters( 'awsm_job_listing_item_class', $classes, $job_id );
		return sprintf( 'class="%s"', esc_attr( join( ' ', $classes ) ) );
	}
}

if ( ! function_exists( 'awsm_jobs_data_attrs' ) ) {
	function awsm_jobs_data_attrs( $attrs = array(), $shortcode_atts = array() ) {
		$content = '';
		$attrs   = array_merge( AWSM_Job_Openings::get_job_listing_data_attrs( $shortcode_atts ), $attrs );
		if ( ! empty( $attrs ) ) {
			foreach ( $attrs as $name => $value ) {
				if ( ! empty( $value ) ) {
					$content .= sprintf( ' data-%s="%s"', esc_attr( $name ), esc_attr( $value ) );
				}
			}
		}
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'awsm_job_content_class' ) ) {
	function awsm_job_content_class( $class = '' ) {
		$content_class = 'awsm-job-single-wrap' . awsm_jobs_wrapper_class( false ) . AWSM_Job_Openings::get_job_details_class();
		if ( ! empty( $class ) ) {
			$content_class .= ' ' . $class;
		}
		printf( 'class="%s"', esc_attr( $content_class ) );
	}
}

if ( ! function_exists( 'is_awsm_job_expired' ) ) {
	function is_awsm_job_expired( $hard_check = true ) {
		$is_expired = get_post_status() === 'expired';
		if ( $hard_check === false ) {
			$is_expired = $is_expired && get_option( 'awsm_jobs_expired_jobs_content_details' ) === 'content';
		}
		return $is_expired;
	}
}

if ( ! function_exists( 'get_awsm_job_details' ) ) {
	function get_awsm_job_details() {
		return array(
			'id'        => get_the_ID(),
			'title'     => get_the_title(),
			'permalink' => get_permalink(),
		);
	}
}

if ( ! function_exists( 'awsm_job_expiry_details' ) ) {
	function awsm_job_expiry_details( $before = '', $after = '' ) {
		$expiry_details = AWSM_Job_Openings::get_job_expiry_details( get_the_ID(), get_post_status() );
		if ( ! empty( $expiry_details ) ) {
			echo $before . $expiry_details . $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'awsm_job_spec_content' ) ) {
	function awsm_job_spec_content( $pos ) {
		AWSM_Job_Openings::display_specifications_content( get_the_ID(), $pos );
	}
}

if ( ! function_exists( 'awsm_job_listing_spec_content' ) ) {
	function awsm_job_listing_spec_content( $job_id, $awsm_filters, $listing_specs, $has_term_link = true ) {
		echo AWSM_Job_Openings::get_specifications_content( $job_id, false, $awsm_filters, array( 'specs' => $listing_specs ), $has_term_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'awsm_job_more_details' ) ) {
	function awsm_job_more_details( $link, $view ) {
		$more_dtls_link = sprintf( '<div class="awsm-job-more-container"><%1$s class="awsm-job-more"%3$s>%2$s <span></span></%1$s></div>', ( $view === 'grid' ) ? 'span' : 'a', esc_html__( 'More Details', 'wp-job-openings' ), ( $view === 'grid' ) ? '' : ' href="' . esc_url( $link ) . '"' );
		echo apply_filters( 'awsm_jobs_listing_details_link', $more_dtls_link, $view ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'awsm_jobs_paginate_links' ) ) {
	function awsm_jobs_paginate_links( $query, $shortcode_atts = array() ) {
		$current       = ( $query->query_vars['paged'] ) ? (int) $query->query_vars['paged'] : 1;
		$max_num_pages = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;

		$base_url = get_pagenum_link();
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['awsm_pagination_base'] ) ) {
			$base_url = $_POST['awsm_pagination_base'];
		}
		// phpcs:enable

		$args               = array(
			'base'    => esc_url_raw( add_query_arg( 'paged', '%#%', $base_url ) ),
			'format'  => '',
			'type'    => 'list',
			'current' => max( 1, $current ),
			'total'   => $max_num_pages,
		);
		$pagination_content = sprintf( '<div class="awsm-jobs-pagination awsm-load-more-classic" data-effect-duration="slow">%s</div>', paginate_links( $args ) );
		/**
		 * Filters the paginate links content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $pagination_content The HTML content.
		 * @param WP_Query $query The Query object.
		 * @param array $args Paginate links arguments.
		 * @param array $shortcode_atts Shortcode attributes.
		 */
		return apply_filters( 'awsm_jobs_paginate_links_content', $pagination_content, $query, $args, $shortcode_atts );
	}
}

if ( ! function_exists( 'awsm_jobs_load_more' ) ) {
	function awsm_jobs_load_more( $query, $shortcode_atts = array() ) {
		$loadmore      = isset( $shortcode_atts['loadmore'] ) && $shortcode_atts['loadmore'] === 'no' ? false : true;
		$max_num_pages = $query->max_num_pages;
		if ( $loadmore && $max_num_pages > 1 ) {
			if ( AWSM_Job_Openings::is_default_pagination( $shortcode_atts ) ) {
				$paged = ( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1;
				if ( $paged < $max_num_pages ) {
					$load_more_content = sprintf( '<div class="awsm-jobs-pagination awsm-load-more-main"><a href="#" class="awsm-load-more awsm-load-more-btn" data-page="%2$s">%1$s</a></div>', esc_html__( 'Load more...', 'wp-job-openings' ), esc_attr( $paged ) );
					/**
					 * Filters the load more content.
					 *
					 * @since 2.3.0
					 *
					 * @param string $load_more_content The HTML content.
					 * @param WP_Query $query The Query object.
					 * @param array $shortcode_atts Shortcode attributes.
					 */
					echo apply_filters( 'awsm_jobs_load_more_content', $load_more_content, $query, $shortcode_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} else {
				echo awsm_jobs_paginate_links( $query );
			}
		}
	}
}

if ( ! function_exists( 'awsm_no_jobs_msg' ) ) {
	function awsm_no_jobs_msg() {
		$msg = get_option( 'awsm_default_msg', __( 'We currently have no job openings', 'wp-job-openings' ) );
		echo wp_kses( $msg, AWSM_Job_Openings_Form::get_allowed_html() );
	}
}

if ( ! function_exists( 'awsm_jobs_expired_msg' ) ) {
	function awsm_jobs_expired_msg( $before = '', $after = '' ) {
		$msg = esc_html__( 'Sorry! This job has expired.', 'wp-job-openings' );
		/**
		 * Filters the expired job content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content The HTML content.
		 * @param string $msg Expired message.
		 * @param string $before The content before expired message.
		 * @param string $after The content after expired message.
		 */
		$msg_content = apply_filters( 'awsm_job_expired_content', $before . $msg . $after, $msg, $before, $after );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $msg_content;
	}
}

if ( ! function_exists( 'awsm_job_form_submit_btn' ) ) {
	function awsm_job_form_submit_btn( $form_attrs ) {
		/**
		 * Filters the application submit button text.
		 *
		 * @since 1.0.0
		 * @since 2.2.0 The `$form_attrs` parameter was added.
		 *
		 * @param array $form_attrs Attributes array for the form.
		 */
		$text = apply_filters( 'awsm_application_form_submit_btn_text', __( 'Submit', 'wp-job-openings' ), $form_attrs );
		/**
		 * Filters the application submit button text on submission.
		 *
		 * @since 1.0.0
		 * @since 2.2.0 The `$form_attrs` parameter was added.
		 *
		 * @param array $form_attrs Attributes array for the form.
		 */
		$res_text = apply_filters( 'awsm_application_form_submit_btn_res_text', __( 'Submitting..', 'wp-job-openings' ), $form_attrs );
		?>
		<input type="submit" name="form_sub" id="<?php echo $form_attrs['single_form'] ? 'awsm-application-submit-btn' : esc_attr( 'awsm-application-submit-btn-' . $form_attrs['job_id'] ); ?>" class="awsm-application-submit-btn" value="<?php echo esc_attr( $text ); ?>" data-response-text="<?php echo esc_attr( $res_text ); ?>" />
		<?php
	}
}

if ( ! function_exists( 'awsm_jobs_featured_image' ) ) {
	function awsm_jobs_featured_image( $echo = true, $size = 'thumbnail', $attr = '' ) {
		$content                = '';
		$post_thumbnail_id      = get_post_thumbnail_id();
		$featured_image_support = get_option( 'awsm_jobs_enable_featured_image' );
		if ( $featured_image_support === 'enable' && $post_thumbnail_id ) {
			$content = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );
		}
		/**
		 * Filters the featured image content.
		 *
		 * @since 2.1.0
		 *
		 * @param string $content The image content.
		 * @param int $post_thumbnail_id The post thumbnail ID.
		 */
		$content = apply_filters( 'awsm_jobs_featured_image_content', $content, $post_thumbnail_id );
		if ( ! empty( $content ) ) {
			$content = '<div class="awsm-job-featured-image">' . $content . '</div>';
		}
		if ( $echo ) {
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $content;
		}
	}
}

if ( ! function_exists( 'awsm_jobs_archive_title' ) ) {
	function awsm_jobs_archive_title() {
		if ( is_archive() ) {
			$title = get_the_archive_title();
			if ( is_post_type_archive( 'awsm_job_openings' ) ) {
				$title = post_type_archive_title( '', false );
			}
			printf( '<h1 class="page-title awsm-jobs-archive-title">%s</h1>', $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
add_action( 'before_awsm_jobs_listing', 'awsm_jobs_archive_title' );

if ( ! function_exists( 'awsm_jobs_single_title' ) ) {
	function awsm_jobs_single_title() {
		the_title( '<h1 class="entry-title awsm-jobs-single-title">', '</h1>' );
	}
}
add_action( 'before_awsm_jobs_single_content', 'awsm_jobs_single_title' );

if ( ! function_exists( 'awsm_jobs_single_featured_image' ) ) {
	function awsm_jobs_single_featured_image() {
		awsm_jobs_featured_image( true, 'post-thumbnail' );
	}
}
add_action( 'before_awsm_jobs_single_content', 'awsm_jobs_single_featured_image' );
