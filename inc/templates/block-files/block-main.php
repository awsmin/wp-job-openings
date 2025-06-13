<?php
/**
 * Main template part for job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes   = isset( $attributes ) ? $attributes : array(); 
$view         = isset( $attributes['layout'] ) ? $attributes['layout'] : get_option( 'awsm_jobs_listing_view' );
$awsm_filters = get_option( 'awsm_jobs_filter' );
//$listing_specs = isset( $attributes['other_options'] ) ? $attributes['other_options'] : '';
//$listing_specs = awsm_block_job_filters_explode( $listing_specs );
$listing_specs = array( 'job-category', 'job-location' );
/**
 * Fires before The Loop to query for jobs.
 *
 * @since 3.5.0
 *
 * @param array $attributes Attributes array from block side .
 */
do_action( 'before_awsm_block_jobs_listing_loop', $attributes );

$styles = hz_get_ui_styles( $attributes ); //echo '<pre>';print_r($styles);
?>

<!-- Styles for css variables -->
<style>
<?php
$block_style_variables = "
	#{$styles['block_id']} {
		--hz-sf-border-width: {$styles['border_width']};
		--hz-sf-border-color: {$styles['border_color']};

		--hz-sf-border-radius-topleft: {$styles['sf_border_radius_topleft']};
		--hz-sf-border-radius-topright: {$styles['sf_border_radius_topright']};
		--hz-sf-border-radius-bottomright: {$styles['sf_border_radius_bottomright']};
		--hz-sf-border-radius-bottomleft: {$styles['sf_border_radius_bottomleft']};

		--hz-sf-padding-left: {$styles['padding_left']};
		--hz-sf-padding-right: {$styles['padding_right']};
		--hz-sf-padding-top: {$styles['padding_top']};
		--hz-sf-padding-bottom: {$styles['padding_bottom']};
		--hz-sf-border-style: " . ( ! empty( $styles['border_width'] ) && $styles['border_width'] !== '0px' ? 'solid' : 'none' ) . ";

		--hz-sidebar-width: {$styles['sidebar_width']};

		--hz-ls-border-width: {$styles['border_width_field']};
		--hz-ls-border-color: {$styles['border_color_field']};

		--hz-ls-border-radius-topleft: {$styles['ls_border_radius_topleft']};
		--hz-ls-border-radius-topright: {$styles['ls_border_radius_topright']};
		--hz-ls-border-radius-bottomright: {$styles['ls_border_radius_bottomright']};
		--hz-ls-border-radius-bottomleft: {$styles['ls_border_radius_bottomleft']};

		--hz-ls-border-style: " . ( ! empty( $styles['border_width_field'] ) && $styles['border_width_field'] !== '0px' ? 'solid' : 'none' ) . ";

		--hz-jl-border-width: {$styles['border_width_jobs']};
		--hz-jl-border-color: {$styles['border_color_jobs']};

		--hz-jl-border-radius-topleft: {$styles['jobs_border_radius_topleft']};
		--hz-jl-border-radius-topright: {$styles['jobs_border_radius_topright']};
		--hz-jl-border-radius-bottomright: {$styles['jobs_border_radius_bottomright']};
		--hz-jl-border-radius-bottomleft: {$styles['jobs_border_radius_bottomleft']};

		--hz-jl-padding-left: {$styles['padding_left_jobs']};
		--hz-jl-padding-right: {$styles['padding_right_jobs']};
		--hz-jl-padding-top: {$styles['padding_top_jobs']};
		--hz-jl-padding-bottom: {$styles['padding_bottom_jobs']};
		--hz-jl-border-style:   " . ( ! empty( $styles['border_width_jobs'] ) && $styles['border_width_jobs'] !== '0px' ? 'solid' : 'none' ) . ";

		--hz-bs-border-width: {$styles['button_width_field']};
		--hz-bs-border-color: {$styles['button_color_field']};

		--hz-bs-border-radius-topleft: {$styles['button_border_radius_topleft']};
		--hz-bs-border-radius-topright: {$styles['button_border_radius_topright']};
		--hz-bs-border-radius-bottomright: {$styles['button_border_radius_bottomright']};
		--hz-bs-border-radius-bottomleft: {$styles['button_border_radius_bottomleft']};

		--hz-b-bg-color: {$styles['button_background_color']};
		--hz-b-tx-color: {$styles['button_text_color']};

		--hz-b-padding-left: {$styles['padding_left_button']};
		--hz-b-padding-right: {$styles['padding_right_button']};
		--hz-b-padding-top: {$styles['padding_top_button']};
		--hz-b-padding-bottom: {$styles['padding_bottom_button']};
	}
	";
	
	echo apply_filters( 'hz_ui_styles_css_variables', $block_style_variables, $styles );

?>
</style>
<!-- End -->

<?php
while ( $query->have_posts() ) {
	$query->the_post();
	$job_details = get_awsm_job_details();

	$attrs  = awsm_jobs_block_listing_item_class( array( "awsm-b-{$view}-item" ) ); 
	$attrs .= sprintf( ' id="awsm-b-%1$s-item-%2$s"', esc_attr( $view ), esc_attr( $job_details['id'] ) );
	echo '<div ' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
		<?php echo ( $view === 'grid' ) ? sprintf( '<a href="%s" class="awsm-b-job-item">', esc_url( $job_details['permalink'] ) ) : '<div class="awsm-b-job-item">'; ?>
			<div class="awsm-b-<?php echo esc_attr( $view ); ?>-left-col">
				<?php
					do_action_deprecated( 'before_awsm_block_jobs_listing_left_col_content', array( $job_details['id'], $attributes ), '3.0.0', 'before_awsm_block_jobs_listing_title' );
				?>
				
				<?php
					$featured_image = 'thumbnail';
				if ( isset( $attributes['featured_image_size'] ) && $attributes['featured_image_size'] != '' ) {
					$featured_image = $attributes['featured_image_size'];
				}
					awsm_jobs_block_featured_image( true, $featured_image, '', $attributes );
				?>

				<h2 class="awsm-b-job-post-title">
					<?php
						$job_title = ( $view === 'grid' ) ? esc_html( $job_details['title'] ) : sprintf( '<a href="%2$s">%1$s</a>', esc_html( $job_details['title'] ), esc_url( $job_details['permalink'] ) );
						echo apply_filters( 'awsm_jobs_block_listing_title', $job_title, $view ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</h2>

				<?php
				if ( $attributes['layout'] && $attributes['layout'] == 'stack' ) {
					awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs, false );
				}
				?>

				<?php
					/**
					 * after_awsm_jobs_listing_title hook
					 *
					 * @since 3.5.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'after_awsm_block_jobs_listing_title', $job_details['id'], $attributes );

					do_action_deprecated( 'after_awsm_block_jobs_listing_left_col_content', array( $job_details['id'], $attributes ), '3.0.0', 'after_awsm_block_jobs_listing_title' );
				?>
			</div>

			<div class="awsm-b-<?php echo esc_attr( $view ); ?>-right-col">
				<?php

					/**
					 * before_awsm_block_jobs_listing_specs_content hook
					 *
					 * @since 3.5.0
					 *
					 * @param int $job_id The Job ID.
					 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
					 */
					do_action( 'before_awsm_block_jobs_listing_specs_content', $job_details['id'], $attributes );

					do_action_deprecated( 'before_awsm_block_jobs_listing_right_col_content', array( $job_details['id'], $attributes ), '3.0.0', 'before_awsm_block_jobs_listing_specs_content' );

				if ( $attributes['layout'] && $attributes['layout'] != 'stack' ) {
					awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs, false );
				}

					awsm_b_job_more_details( $job_details['permalink'], $view );
				?>
			</div>
		<?php echo ( $view === 'grid' ) ? '</a>' : '</div>'; ?>
	<?php
	echo '</div>';
}

wp_reset_postdata();

awsm_block_jobs_load_more( $query, $attributes );
