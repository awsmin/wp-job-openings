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

if ( $query->have_posts() ) : ?>
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

		<div <?php awsm_block_jobs_view_class( '', $attributes ); ?><?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
			<?php
				include get_awsm_jobs_template_path( 'block-main', 'block-files' );
			?>
		</div>
	</div>
	<?php
else :
	?>
	<div class="jobs-none-container">
		<p><?php awsm_no_jobs_msg(); ?></p>
	</div>
	<?php
endif;
