<?php
/**
 * Template for displaying job openings (used by shortcode too)
 *
 * Override this by copying it to currenttheme/wp-job-openings/job-openings-view.php
 *
 * @package wp-job-openings
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode_atts = isset( $shortcode_atts ) ? $shortcode_atts : array();
/**
 * Fires before the job listing content.
 *
 * @hooked awsm_jobs_archive_title
 *
 * @since 1.1.0
 * @since 2.2.0 The `$shortcode_atts` parameter was added.
 *
 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
 */
do_action( 'before_awsm_jobs_listing', $shortcode_atts );

$query = awsm_jobs_query( $shortcode_atts );

$show_filter             = false;
$placement_sidebar_class = '';

$available_filters = get_option( 'awsm_jobs_listing_available_filters', array() );

if (
	( ! empty( $shortcode_atts['search'] ) && $shortcode_atts['search'] === 'yes' ) ||
	( ! empty( $shortcode_atts['filters'] ) && $shortcode_atts['filters'] === 'yes' && ! empty( $available_filters ) )
) {
	$show_filter             = true;
	$placement_sidebar_class = 'awsm-job-2-col';
}

if ( $query->have_posts() ) {
	if ( $shortcode_atts['placement'] == 'top' ) {
		?>
	<div class="awsm-job-wrap<?php awsm_jobs_wrapper_class(); ?>">
		<?php
			/**
			 * awsm_filter_form hook
			 *
			 * Display filter form for job listings
			 *
			 * @hooked AWSM_Job_Openings_Filters::display_filter_form()
			 *
			 * @since 1.0.0
			 * @since 1.3.0 The `$shortcode_atts` parameter was added.
			 *
			 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
			 */
			do_action( 'awsm_filter_form', $shortcode_atts );
			do_action( 'awsm_filter_after_form' );
		?>
		
		<div class="awsm-job-listings"<?php awsm_jobs_data_attrs( array(), $shortcode_atts ); ?>>
			<?php if ( isset( $shortcode_atts['sort'] ) && $shortcode_atts['sort'] == 'enable' ) { ?>
				<div class="awsm-job-sort-wrap">
					<div class="awsm-job-results" id="awsm-job-count"></div> 
					<?php
						/**
						 * awsm_block_jobs_sort hook
						 *
						 * Display sort filter for job listings
						 *
						 * @hooked AWSM_Job_Openings_Block::display_block_job_sort()
						 *
						 * @since 3.5.0
						 *
						 * @param array $attributes Attributes array from block.
						 */
						do_action( 'awsm_jobs_sort', $shortcode_atts );
					?>
				</div>
			<?php } ?>
			<div <?php awsm_jobs_view_class( '', $shortcode_atts ); ?>>
				<?php
					include get_awsm_jobs_template_path( 'main', 'job-openings' );
				?>
			</div>
		</div>

	</div>
		<?php
	} else {
		?>
		<div class="awsm-job-wrap<?php awsm_jobs_wrapper_class(); ?> awsm-job-form-plugin-style <?php echo $placement_sidebar_class; ?>">
		<?php if ( $show_filter ) { ?>
		<div class="awsm-filter-wrap awsm-jobs-alerts-on">
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
				do_action( 'awsm_filter_form_slide', $shortcode_atts );
				do_action( 'awsm_form_outside', $shortcode_atts );
			?>
		</div>
		<?php } ?>

		<div class="awsm-job-listings"<?php awsm_jobs_data_attrs( array(), $shortcode_atts ); ?>>
		<?php if ( isset( $shortcode_atts['sort'] ) && $shortcode_atts['sort'] == 'enable' ) { ?>
			<div class="awsm-job-sort-wrap">
				<div class="awsm-job-results" id="awsm-job-count"></div> 
				<?php
					/**
					 * awsm_block_jobs_sort hook
					 *
					 * Display sort filter for job listings
					 *
					 * @hooked AWSM_Job_Openings_Block::display_block_job_sort()
					 *
					 * @since 3.5.0
					 *
					 * @param array $attributes Attributes array from block.
					 */
					do_action( 'awsm_jobs_sort', $shortcode_atts );
				?>
			</div>
		<?php } ?>
			<div <?php awsm_jobs_view_class( '', $shortcode_atts ); ?>>
				<?php include get_awsm_jobs_template_path( 'main', 'job-openings' ); ?>
			</div>
		</div>
	</div>
		<?php
	}
}
/* else :
	$filter_suffix = '_spec';
	$job_spec      = array();

	if ( ! empty( $_GET ) ) {
		foreach ( $_GET as $key => $value ) {
			if ( substr( $key, -strlen( $filter_suffix ) ) === $filter_suffix ) {
				$job_spec[ $key ] = sanitize_text_field( $value );
			} // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	if ( ! empty( $job_spec ) ) {
		?>
			<div class="awsm-job-wrap<?php awsm_jobs_wrapper_class(); ?>">
				<?php
					do_action( 'awsm_filter_form', $shortcode_atts );
					do_action( 'awsm_filter_after_form' );
				?>
				<?php
				get_filtered_job_terms();
				$no_jobs_content = sprintf(
					'<div class="awsm-jobs-pagination awsm-load-more-main awsm-no-more-jobs-container awsm-job-no-more-jobs-get"><p>%s</p></div>',
					esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' )
				);
				echo $no_jobs_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<div <?php awsm_jobs_view_class( '', $shortcode_atts ); ?><?php awsm_jobs_data_attrs( array(), $shortcode_atts ); ?>>
					<?php include get_awsm_jobs_template_path( 'main', 'job-openings' ); ?>
				</div>
			</div>
		<?php
	} else {
		?>
		<div class="jobs-none-container">
			<p><?php awsm_no_jobs_msg(); ?></p>
		</div>
		<?php
	}*/


/**
 * Fires after the job listing content.
 *
 * @since 1.1.0
 * @since 2.2.0 The `$shortcode_atts` parameter was added.
 *
 * @param array $shortcode_atts Attributes array if shortcode is used, else an empty array.
 */
//do_action( 'after_awsm_jobs_listing', $shortcode_atts );
