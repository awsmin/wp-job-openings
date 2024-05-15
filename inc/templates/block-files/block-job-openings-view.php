<?php
/**
 * Template for displaying job openings in block
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $block_atts_set ) ? $block_atts_set : array(); 
$query = awsm_jobs_query( $attributes );

if ( $query->have_posts() ) : ?>
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
			do_action( 'awsm_block_filter_form', $attributes );
		?>

		<div <?php awsm_jobs_view_class_block( '', $attributes ); ?><?php awsm_jobs_data_attrs( array(), $attributes ); ?>>
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
