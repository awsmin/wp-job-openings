<?php
/**
 * Template for displaying job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $block_atts_set ) && is_array( $block_atts_set ) ? $block_atts_set : array();
$placement  = isset( $attributes['placement'] ) ? $attributes['placement'] : '';
$query      = awsm_block_jobs_query( $attributes );
$block_id   = ( isset( $attributes['block_id'] ) && trim( $attributes['block_id'] ) !== '' ) ? $attributes['block_id'] : 'awsm-block-' . wp_unique_id();

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
if ( isset( $attributes['search'] ) && $attributes['search'] === 'enable' ) {
	$show_filter             = true;
	$placement_sidebar_class = 'awsm-job-2-col';
}

$wrapper_class = 'awsm-b-job-wrap' . awsm_jobs_wrapper_class( false );

// Add sidebar placement class only if placement is side
if ( $placement === 'side' && ! empty( $placement_sidebar_class ) ) {
	$wrapper_class .= ' ' . $placement_sidebar_class;
}

// Add plugin style class if placement is not top
if ( 'top' !== $placement ) {
	$wrapper_class .= ' awsm-job-form-plugin-style';
}

if ( function_exists( 'get_block_wrapper_attributes' ) ) {
	$wrapper_attrs = get_block_wrapper_attributes(
		array(
			'class' => $wrapper_class,
			'id'    => $block_id,
		)
	);
} else {
	$wrapper_attrs = 'class="' . esc_attr( $wrapper_class ) . '" id="' . esc_attr( $block_id ) . '"';
}

if ( $placement === 'top' ) {
	?>
		<div <?php echo $wrapper_attrs; ?>>
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
	<?php
} else {
	?>
		<div <?php echo $wrapper_attrs; ?>>
			<?php if ( $show_filter ) { ?>
				<div class="awsm-b-filter-wrap awsm-jobs-alerts-on">
					<?php
						/**
						 * awsm_block_filter_form_side hook
						 *
						 * Display filter form  in placement side for job listings
						 *
						 * @hooked AWSM_Job_Openings_Block::display_block_filter_form_side()
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
		<?php
	}
	?>
			<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
				<div <?php echo awsm_block_jobs_view_class( $placement === 'top' ? '' : 'custom-class', $attributes ); ?>>
					<?php if ( $query->have_posts() ) : ?>
						<?php
						include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					<?php else : ?>
						<div class="awsm-jobs-none-container awsm-b-jobs-none-container">
							<p><?php awsm_no_jobs_msg(); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
