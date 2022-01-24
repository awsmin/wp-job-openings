<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	wp_nonce_field( 'awsm_save_post_meta', 'awsm_jobs_posts_nonce' );
	$display_list   = get_post_meta( get_the_ID(), 'awsm_exp_list_display', true );
	$expiry_on_list = get_post_meta( get_the_ID(), 'awsm_set_exp_list', true );

	/**
	 * Initialize job expiry meta box.
	 *
	 * @since 1.6.0
	 */
	do_action( 'awsm_job_expiry_mb_init', $post->ID );
?>

<div class="awsm-form-section">
	<div class="awsm-job-expiry-items">
		<?php
			/**
			 * Fires before job expiry meta box content.
			 *
			 * @since 1.6.0
			 */
			do_action( 'before_awsm_job_expiry_mb_content', $post->ID );
		?>
		<input type="checkbox" name="awsm_set_exp_list" class="awsm-check-control-field" id="awsm-job-expiry" value="set_listing"<?php echo ( $expiry_on_list === 'set_listing' ) ? 'checked' : ''; ?> data-req-target="#awsm-jobs-datepicker" /><label for="awsm-job-expiry"><?php esc_html_e( 'Set expiry for listing', 'wp-job-openings' ); ?></label>

		<div class="awsm-job-expiry-main">
			<p>
				<?php
					$date_format     = get_awsm_jobs_date_format( 'expiry-admin' );
					$awsm_job_expiry = get_post_meta( $post->ID, 'awsm_job_expiry', true );
				?>
				<div class="awsm-jobs-datepicker-wrapper"><input type="text" class="awsm-jobs-datepicker" id="awsm-jobs-datepicker" name="awsm_job_expiry_text_field" placeholder="<?php echo esc_attr( $date_format ); ?>" value="<?php echo ( ! empty( $awsm_job_expiry ) ) ? esc_attr( date_i18n( $date_format, strtotime( $awsm_job_expiry ) ) ) : ''; ?>" /><input type="hidden" id="awsm-jobs-datepicker-alt" name="awsm_job_expiry" value="<?php echo esc_attr( $awsm_job_expiry ); ?>" /></div>
			</p>
			<p>
				<label for="awsm-job-expiry-display"><input type="checkbox" name="awsm_exp_list_display" id="awsm-job-expiry-display" value="list_display"<?php echo ( $display_list === 'list_display' ) ? 'checked' : ''; ?>   /><?php esc_html_e( 'Display expiry date', 'wp-job-openings' ); ?></label>
			</p>
		</div>
		<?php
			/**
			 * Fires after job expiry meta box content.
			 *
			 * @since 1.6.0
			 */
			do_action( 'after_awsm_job_expiry_mb_content', $post->ID );
		?>
	</div>
</div>


