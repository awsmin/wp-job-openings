<?php
/**
 * Template for displaying job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes              = isset( $block_atts_set ) && is_array( $block_atts_set ) ? $block_atts_set : array();
$placement               = isset( $attributes['placement'] ) ? $attributes['placement'] : '';
$query                   = awsm_block_jobs_query( $attributes );
$show_filter             = false;
$placement_sidebar_class = '';
$styles                  = hz_get_ui_styles( $attributes );
$block_id                = $styles['block_id'];
?>

<!-- Styles for css variables -->
<style>
<?php
$block_style_variables = "
	#{$styles['block_id']} {
		--hz-sf-border-width: {$styles['border_width']};
		" . ( ! empty( $styles['border_color'] ) ? "--hz-sf-border-color: {$styles['border_color']};" : '' ) . "

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
		" . ( ! empty( $styles['border_color_field'] ) ? "--hz-ls-border-color: {$styles['border_color_field']};" : '' ) . "

		--hz-ls-border-radius-topleft: {$styles['ls_border_radius_topleft']};
		--hz-ls-border-radius-topright: {$styles['ls_border_radius_topright']};
		--hz-ls-border-radius-bottomright: {$styles['ls_border_radius_bottomright']};
		--hz-ls-border-radius-bottomleft: {$styles['ls_border_radius_bottomleft']};

		--hz-ls-border-style: " . ( ! empty( $styles['border_width_field'] ) && $styles['border_width_field'] !== '0px' ? 'solid' : 'none' ) . ";

		--hz-jl-border-width: {$styles['border_width_jobs']};
		" . ( ! empty( $styles['border_color_jobs'] ) ? "--hz-jl-border-color: {$styles['border_color_jobs']};" : '' ) . "

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
		" . ( ! empty( $styles['button_color_field'] ) ? "--hz-bs-border-color: {$styles['button_color_field']};" : '' ) . "

		--hz-bs-border-radius-topleft: {$styles['button_border_radius_topleft']};
		--hz-bs-border-radius-topright: {$styles['button_border_radius_topright']};
		--hz-bs-border-radius-bottomright: {$styles['button_border_radius_bottomright']};
		--hz-bs-border-radius-bottomleft: {$styles['button_border_radius_bottomleft']};
		--hz-bs-border-style:   " . ( ! empty( $styles['button_width_field'] ) && $styles['button_width_field'] !== '0px' ? 'solid' : 'none' ) . ';

		' . ( ! empty( $styles['button_background_color'] ) ? "--hz-b-bg-color: {$styles['button_background_color']};" : '' ) . '
		' . ( ! empty( $styles['button_text_color'] ) ? "--hz-b-tx-color: {$styles['button_text_color']};" : '' ) . '

		' . ( ! empty( $styles['pagination_background_color'] ) ? "--hz-pagination-bg-color: {$styles['pagination_background_color']};" : '' ) . '
		' . ( ! empty( $styles['pagination_text_color'] ) ? "--hz-pagination-tx-color: {$styles['pagination_text_color']};" : '' ) . "
		--hz-pagination-border-width: {$styles['pagination_border_width']};
		" . ( ! empty( $styles['pagination_border_color'] ) ? "--hz-pagination-border-color: {$styles['pagination_border_color']};" : '' ) . '
		--hz-pagination-border-style: ' . ( ! empty( $styles['pagination_border_width'] ) && $styles['pagination_border_width'] !== '0px' ? 'solid' : 'none' ) . ";
		--hz-pagination-border-radius-topleft: {$styles['pagination_border_radius_topleft']};
		--hz-pagination-border-radius-topright: {$styles['pagination_border_radius_topright']};
		--hz-pagination-border-radius-bottomright: {$styles['pagination_border_radius_bottomright']};
		--hz-pagination-border-radius-bottomleft: {$styles['pagination_border_radius_bottomleft']};

		" . ( ! empty( $styles['sf_background_color'] ) ? "--hz-sf-bg-color: {$styles['sf_background_color']};" : '' ) . '
		' . ( ! empty( $styles['sf_text_color'] ) ? "--hz-sf-tx-color: {$styles['sf_text_color']};" : '' ) . '

		' . ( ! empty( $styles['jl_background_color'] ) ? "--hz-jl-bg-color: {$styles['jl_background_color']};" : '' ) . '
		' . ( ! empty( $styles['jl_text_color'] ) ? "--hz-jl-tx-color: {$styles['jl_text_color']};" : '' ) . '

		' . ( ! empty( $styles['sidebar_bg_color'] ) ? "--hz-sidebar-bg-color: {$styles['sidebar_bg_color']};" : '' ) . '
		' . ( ! empty( $styles['sidebar_tx_color'] ) ? "--hz-sidebar-tx-color: {$styles['sidebar_tx_color']};" : '' ) . "

		--hz-b-padding-left: {$styles['padding_left_button']};
		--hz-b-padding-right: {$styles['padding_right_button']};
		--hz-b-padding-top: {$styles['padding_top_button']};
		--hz-b-padding-bottom: {$styles['padding_bottom_button']};
	}
	";

	echo apply_filters( 'hz_ui_styles_css_variables', $block_style_variables, $styles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</style>
<!-- End -->
<?php
$has_search  = ( isset( $attributes['search'] ) && $attributes['search'] === 'enable' );
$has_filters = (
	isset( $attributes['enable_job_filter'] ) &&
	$attributes['enable_job_filter'] === 'enable' &&
	isset( $attributes['filter_options'] ) &&
	is_array( $attributes['filter_options'] ) &&
	! empty( $attributes['filter_options'] )
);

// If side placement has no search/filters, show the sidebar only when Job Alerts add-on is enabled
// so the trigger button doesn't jump above the listings.
$has_alerts = (
	'side' === $placement &&
	! empty( $attributes['enable_alert'] ) &&
	class_exists( 'AWSM_Job_Openings_Alert_Main_Blocks' )
);

if ( $has_search || $has_filters || $has_alerts ) {
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
		<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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
			<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $show_filter ) { ?>
					<?php
					// Only use the Selectric loading state when rendering actual search/filters.
					$filter_wrap_loading_class = ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( ! $has_search && ! $has_filters ) )
						? ''
						: ' awsm-selectric-loading';
					?>
					<div class="awsm-b-filter-wrap<?php echo ! $has_search ? ' awsm-b-no-search-filter-wrap' : ''; ?><?php echo esc_attr( $filter_wrap_loading_class ); ?><?php echo ( class_exists( 'AWSM_Job_Openings_Alert_Main_Blocks' ) && ! empty( $attributes['enable_alert'] ) ) ? ' awsm-jobs-alerts-on' : ''; ?>" data-placement="side">
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
						// If only alerts are enabled (no search/filters), render the inside content directly.
						if ( ! $has_search && ! $has_filters ) {
							do_action( 'awsm_block_form_inside', $attributes );
						} else {
							do_dynamic_filter_form_action( $attributes );
						}
						?>
					</div>
				<?php } else { ?>
				<?php
				// If no sidebar, render awsm_block_form_inside content in a wrapper above the listings.
				ob_start();
				do_action( 'awsm_block_form_inside', $attributes );
				$extra_inside = ob_get_clean();
				if ( ! empty( $extra_inside ) ) {
					echo '<div class="awsm-b-filter-wrap-extra' . ( ( class_exists( 'AWSM_Job_Openings_Alert_Main_Blocks' ) && ! empty( $attributes['enable_alert'] ) ) ? ' awsm-jobs-alerts-on' : '' ) . '">' . $extra_inside . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
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
				<div <?php awsm_block_jobs_view_class( $placement === 'top' ? '' : 'custom-class', $attributes ); ?>>
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
				<?php awsm_block_jobs_load_more( $query, $attributes ); ?>
			</div>
		</div>
