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

$wrapper_class = 'awsm-b-job-wrap' . awsm_jobs_wrapper_class( false );

if ( function_exists( 'get_block_wrapper_attributes' ) ) {
	$wrapper_attrs = get_block_wrapper_attributes(
		array(
			'class' => $wrapper_class,
		)
	);
} else {
	$wrapper_attrs = 'class="' . esc_attr( $wrapper_class ) . '"';
}

if ( $query->have_posts() ) : ?>
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
			do_action( 'awsm_block_filter_form', $attributes );
			do_action( 'awsm_block_form_outside', $attributes );
		?>

		<div <?php awsm_block_jobs_view_class( '', $attributes ); ?><?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
			<?php
				include get_awsm_jobs_template_path( 'block-main', 'block-files' );
			?>
		</div>
	</div>
	<?php
else :
	$filter_suffix = '_spec';
	$job_spec      = array();

	if ( ! empty( $_GET ) ) {
		foreach ( $_GET as $key => $value ) {
			if ( substr( $key, -strlen( $filter_suffix ) ) === $filter_suffix ) {
				$job_spec[ $key ] = sanitize_text_field( $value );
			}
		}
		if ( isset( $_GET['jq'] ) && $_GET['jq'] !== '' ) {
			$search_job = sanitize_text_field( wp_unslash( $_GET['jq'] ) );
		}
	}

	if ( ! empty( $job_spec ) || ! empty( $search_job ) ) {
		?>
			<div <?php echo $wrapper_attrs; ?>>
				<?php
					do_action( 'awsm_block_filter_form', $attributes );
					do_action( 'awsm_filter_after_form' );
				?>
				<?php
				get_block_filtered_job_terms( $attributes );
				printf(
					'<div class="awsm-b-jobs-pagination awsm-b-load-more-main awsm-b-no-more-jobs-container awsm-b-job-no-more-jobs-get awsm-b-jobs-none-container awsm-b-lists"><p>%s</p></div>',
					esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' )
				);
				?>
				<div <?php awsm_block_jobs_view_class( '', $attributes ); ?><?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
				</div>
			</div>
		<?php
	} else {
		?>
		<div class="awsm-b-jobs-none-container">
			<p><?php awsm_no_jobs_msg(); ?></p>
		</div>
		<?php
	}
endif;

