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
						do_action( 'awsm_block_form_outside', $attributes );
					?>
				</div> 
				<?php } ?>
				
				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php echo awsm_block_jobs_view_class( 'custom-class', $attributes ); ?>> 
						<?php
							include get_awsm_jobs_template_path( 'block-main', 'block-files' );
						?>
					</div>
				</div>
			</div>
<?php }
}else { 
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
				echo sprintf(
					'<div class="awsm-jobs-pagination awsm-load-more-main awsm-no-more-jobs-container awsm-b-job-no-more-jobs-get"><p>%s</p></div>',
					esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' )
				);
				?>
				<div <?php awsm_block_jobs_view_class( '', $attributes ); ?> <?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
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
				<div <?php awsm_block_jobs_view_class( 'custom-class', $attributes ); ?> <?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<?php echo sprintf(
						'<div class="awsm-jobs-pagination awsm-load-more-main awsm-no-more-jobs-container awsm-b-job-no-more-jobs-get"><p>%s</p></div>',
						esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' )
					); ?>
					<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
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