<?php
	if( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	$args = AWSM_Job_Openings::awsm_job_query_args();
    $query = new WP_Query( $args );
	if( $query->have_posts() ) : ?>
		<div class="awsm-job-wrap">

			<?php do_action( 'awsm_filter_form' );  ?>

			<div class="<?php echo AWSM_Job_Openings::get_job_listing_view_class(); ?>" id="awsm-job-response">
				<?php
					include_once untrailingslashit( plugin_dir_path( __FILE__ ) ) .  '/partials/listing-view.php';
				?>
			</div>

		</div>
<?php
	else :
?>
		<div class="jobs-none-container">
			<p><?php echo get_option( 'awsm_default_msg' ); ?></p>
		</div>
<?php
	endif;
?>