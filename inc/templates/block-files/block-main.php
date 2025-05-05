<?php
/**
 * Main template part for job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes    = isset( $attributes ) ? $attributes : array();  
$view          = isset( $attributes['layout'] ) ? $attributes['layout'] : get_option( 'awsm_jobs_listing_view' );
$button_style  = isset( $attributes['hz_button_styles'] ) ? $attributes['hz_button_styles'] : 'none'; 
$awsm_filters  = get_option( 'awsm_jobs_filter' );
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

$styles = hz_get_ui_styles($attributes); 
?>

<!--  Styles for css variables  -->
<style>
	#<?php echo esc_attr( $styles['block_id'] ); ?> {
		--hz-sf-border-width: 	<?php echo esc_attr( $styles['border_width'] ); 	    ?>;
		--hz-sf-border-color: 	<?php echo esc_attr( $styles['border_color'] ); 	    ?>;
		--hz-sf-border-radius: 	<?php echo esc_attr( $styles['border_radius'] ); 	    ?>;
		--hz-sf-padding-left: 	<?php echo esc_attr( $styles['padding_left'] ); 	    ?>;
		--hz-sf-padding-right: 	<?php echo esc_attr( $styles['padding_right'] ); 	    ?>;
		--hz-sf-padding-top: 	<?php echo esc_attr( $styles['padding_top'] ); 		    ?>;
		--hz-sf-padding-bottom: <?php echo esc_attr( $styles['padding_bottom'] ); 	    ?>;

		--hz-sidebar-width: 	<?php echo esc_attr( $styles['sidebar_width'] ); 		?>;
		--hz-ls-border-width: 	<?php echo esc_attr( $styles['border_width_field'] );   ?>;
		--hz-ls-border-color: 	<?php echo esc_attr( $styles['border_color_field'] );   ?>;
		--hz-ls-border-radius: 	<?php echo esc_attr( $styles['border_radius_field'] );  ?>;

		--hz-jl-border-width: 	<?php echo esc_attr( $styles['border_width_jobs'] );    ?>;
		--hz-jl-border-color: 	<?php echo esc_attr( $styles['border_color_jobs'] );    ?>;
		--hz-jl-border-radius: 	<?php echo esc_attr( $styles['border_radius_jobs'] );   ?>;
		--hz-jl-padding-left: 	<?php echo esc_attr( $styles['padding_left_jobs'] ); 	?>;
		--hz-jl-padding-right: 	<?php echo esc_attr( $styles['padding_right_jobs'] ); 	?>;
		--hz-jl-padding-top: 	<?php echo esc_attr( $styles['padding_top_jobs'] );     ?>;
		--hz-jl-padding-bottom: <?php echo esc_attr( $styles['padding_bottom_jobs'] ); 	?>;

		--hz-bs-border-width: 	<?php echo esc_attr( $styles['button_width_field'] );   ?>;
		--hz-bs-border-color: 	<?php echo esc_attr( $styles['button_color_field'] );   ?>;
		--hz-bs-border-radius: 	<?php echo esc_attr( $styles['button_radius_field'] );  ?>;

		--hz-b-bg-color: 	<?php echo esc_attr( $styles['button_background_color'] );  ?>;
		--hz-b-tx-color: 	<?php echo esc_attr( $styles['button_text_color'] );  ?>;
	}
</style>
<!-- End -->

<?php
while ( $query->have_posts() ) {
	$query->the_post();
	$job_details = get_awsm_job_details(); error_log( 'Post ID: ' . get_the_ID() );

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
					if( $attributes['layout'] && $attributes['layout'] == 'stack' ){
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

					if( $attributes['layout'] && $attributes['layout'] != 'stack' ){
						awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs, false );
					}

					awsm_b_job_more_details( $job_details['permalink'], $view ,$button_style);
				?>
			</div>
		<?php echo ( $view === 'grid' ) ? '</a>' : '</div>'; ?>
	<?php
	echo '</div>';
}

wp_reset_postdata();

awsm_block_jobs_load_more( $query, $attributes );
