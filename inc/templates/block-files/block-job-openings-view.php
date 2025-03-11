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

$show_filter             = false;
$placement_sidebar_class = '';

if ( isset( $attributes['search'] ) && $attributes['search'] == 'enable' ) {
	$show_filter             = true;
	$placement_sidebar_class = 'awsm-job-2-col';
}

if ( $query->have_posts() ) :
	if ( $attributes['placement'] == 'top' ) {
		?>
		<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?>">
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
			do_action( 'awsm_block_filter_form', $attributes );
			do_action( 'awsm_block_form_outside', $attributes );
			?>
			
			<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
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
					do_action( 'awsm_block_jobs_sort', $attributes );
				?>

			</div> 
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
	<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?> awsm-job-form-plugin-style <?php echo $placement_sidebar_class; ?>">
		<?php if ( $show_filter ) { ?>
		<div class="awsm-b-filter-wrap awsm-jobs-alerts-on">
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
				do_action( 'awsm_block_filter_form_slide', $attributes );
				do_action( 'awsm_block_form_outside', $attributes );
			?>
		</div>
		<?php } ?>

		<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
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
					do_action( 'awsm_block_jobs_sort', $attributes );
				?>

			</div> 
			<div <?php awsm_block_jobs_view_class( '', $attributes ); ?>>
				<?php
					include get_awsm_jobs_template_path( 'block-main', 'block-files' );
				?>
			</div>
		</div>
	</div>
<?php } ?>
	<?php
	/* else :
		$filter_suffix = '_spec';
		$job_spec      = array();

		if ( ! empty( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( substr( $key, -strlen( $filter_suffix ) ) === $filter_suffix ) {
					$job_spec[ $key ] = sanitize_text_field( $value );
				}
			}
		}

		if ( ! empty( $job_spec ) ) {
			?>
				<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?>">
					<?php
						do_action( 'awsm_block_filter_form', $attributes );
						do_action( 'awsm_block_form_outside', $attributes );
					?>
					<?php
					get_block_filtered_job_terms( $attributes );
					$no_jobs_content = sprintf(
						'<div class="awsm-jobs-pagination awsm-load-more-main awsm-no-more-jobs-container awsm-b-job-no-more-jobs-get"><p>%s</p></div>',
						esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' )
					);
					echo $no_jobs_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<div <?php awsm_block_jobs_view_class( '', $attributes ); ?><?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
						<?php
							include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					</div>
				</div>
			<?php
		} else {
			?>
			<div class="jobs-none-container">
				<p><?php awsm_no_jobs_msg(); ?></p>
			</div>
			<?php
		} */
	endif;
