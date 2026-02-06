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
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() returns escaped HTML attributes ?>>
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
		<?php if ( $query->have_posts() ) : ?>
			<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
		<?php else : ?>
			<div class="awsm-jobs-none-container awsm-b-jobs-none-container">
				<p><?php awsm_no_jobs_msg(); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
/**
 * Fires after the job listing content.
 *
 * @param array $attributes Attributes array from block.
 */
do_action( 'after_awsm_block_jobs_listing', $attributes );
?>
