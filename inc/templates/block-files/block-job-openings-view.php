<?php
/**
 * Template for displaying job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $block_atts_set ) ? $block_atts_set : array();
$query      = awsm_block_jobs_query( $attributes );
$block_id   = ( isset( $attributes['block_id'] ) && trim( $attributes['block_id'] ) !== '' ) ? $attributes['block_id'] : 'default-block-id';

$show_filter             = false;
$placement_sidebar_class = '';
$styles                  = hz_get_ui_styles( $attributes ); 
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
		--hz-bs-border-style:   " . ( ! empty( $styles['button_width_field'] ) && $styles['button_width_field'] !== '0px' ? 'solid' : 'none' ) . ";

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
if ( isset( $attributes['search'] ) && $attributes['search'] == 'enable' ) {
	$show_filter             = true;
	$placement_sidebar_class = 'awsm-job-2-col';
}

if ( $query->have_posts() ) {
	if ( $attributes['placement'] == 'top' ) {
		?>
			<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?>" id="<?php echo esc_attr( $block_id ); ?>">
				<?php
				/**
				 * awsm_block_filter_form hook
				 *
				 * Display filter form for job listings
				 *
				 * @hooked AWSM_Job_Openings_Block::display_block_filter_form()
				 *
				 * @since 3.5.0
				 *
				 * @param array $attributes Attributes array from block.
				 */
				do_dynamic_filter_form_action( $attributes );
				do_action( 'awsm_block_form_outside', $attributes );
				?>
				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php awsm_block_jobs_view_class( '', $attributes ); ?>>
						<?php
							include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					</div>
				</div>
			</div>
		<?php
	} else {
		?>
			<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?> awsm-job-form-plugin-style <?php echo $placement_sidebar_class; ?>" id="<?php echo esc_attr( $block_id ); ?>"> 
				<?php if ( $show_filter ) { ?>
				<div class="awsm-b-filter-wrap awsm-jobs-alerts-on" >
					<?php
						/**
						 * awsm_block_filter_form_slide hook
						 *
						 * Display filter form  in placement slide for job listings
						 *
						 * @hooked AWSM_Job_Openings_Block::display_block_filter_form_slide()
						 *
						 * @since 3.5.0
						 *
						 * @param array $attributes Attributes array from block.
						 */
						do_dynamic_filter_form_action( $attributes );
					?>
				</div> 
				<?php } ?>
				<?php 
					/**
					 * awsm_block_filter_form_extra hook
					 *
					 * Display extra fields if search and filters are not enabled
					 *
					 * @since 4.0.0
					 *
					 * @param array $attributes Attributes array from block.
					 */
					do_action( 'awsm_block_filter_form_extra', $attributes );
					do_action( 'awsm_block_form_outside', $attributes );
				?>
				
				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php echo awsm_block_jobs_view_class( 'custom-class', $attributes ); ?>> 
						<?php
							include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					</div>
				</div>
			</div>
		<?php
	}
} else {
		// When no jobs are found, check for filters in URL
	$filter_suffix = '_spec';
	$job_spec      = array();

	foreach ( $_GET as $key => $value ) {
		if ( substr( $key, -strlen( $filter_suffix ) ) === $filter_suffix ) {
			$job_spec[ $key ] = sanitize_text_field( $value );
		}
	}

	if ( ! empty( $job_spec ) ) {
		if ( $attributes['placement'] === 'top' ) {
			?>
			<div class="awsm-b-job-wrap <?php echo esc_attr( awsm_jobs_wrapper_class( false ) ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
				<?php
				do_dynamic_filter_form_action( $attributes );
				do_action( 'awsm_block_form_outside', $attributes );
				get_block_filtered_job_terms( $attributes );

				?>
				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php awsm_block_jobs_view_class( '', $attributes ); ?>>
						<?php
							include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					</div>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="awsm-b-job-wrap <?php echo esc_attr( awsm_jobs_wrapper_class( false ) ); ?> awsm-job-form-plugin-style <?php echo esc_attr( $placement_sidebar_class ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
				<?php if ( $show_filter ) : ?>
					<div class="awsm-b-filter-wrap awsm-jobs-alerts-on">
						<?php
						do_dynamic_filter_form_action( $attributes );
						do_action( 'awsm_block_form_outside', $attributes );
						?>
					</div>
				<?php endif; ?>
				<?php
				get_block_filtered_job_terms( $attributes );
				?>
				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php echo awsm_block_jobs_view_class( 'custom-class', $attributes ); ?>> 
						<?php
							include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		?>
		<div class="jobs-none-container">
			<p><?php awsm_no_jobs_msg(); ?></p>
		</div>
		<?php
	}
}
