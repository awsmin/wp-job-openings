<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$application_id = $post->ID;
?>

<div class="submitbox awsm-application-actions-disabled" id="submitpost">
	<?php
		/**
		 * Fires before applicant meta box actions.
		 *
		 * @since 1.6.0
		 */
		do_action( 'before_awsm_job_applicant_mb_actions', $application_id );
	?>

	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp">
					<?php
						$formatted_date = date_i18n( get_awsm_jobs_date_format( 'application-actions' ) . ' @ ' . get_awsm_jobs_time_format( 'application-actions' ), strtotime( $post->post_date ) );
						/* translators: %s: application submission time */
						printf( esc_html__( 'Submitted on: %s', 'wp-job-openings' ), sprintf( '<strong>%s</strong>', esc_html( $formatted_date ) ) );
					?>
				</span>
			</div>
			<div class="misc-pub-section">
				<div class="awsm-application-post-status-disabled">
					<p><label for="post_status"><?php esc_html_e( 'Status:', 'wp-job-openings' ); ?></label></p>
					<p>
						<select style="width:100%;" disabled>
							<option selected><?php echo esc_html_x( 'New', 'post status', 'wp-job-openings' ); ?></option>
						</select>
					</p>
				</div>
			</div>
			<!-- Rating -->
			<div class="misc-pub-section awsm-application-rating-pub-section-disabled">
				<div class="awsm-application-rating-disabled">
					<?php
						wp_star_rating(
							array(
								'rating' => 3,
								'type'   => 'rating',
							)
						);
						?>
				</div>
				<div class="awsm-application-pro-features-btn-wrapper">
					<span class="awsm-jobs-get-pro-btn"><?php esc_html_e( 'Pro Features', 'wp-job-openings' ); ?></span>
				</div>
			</div>
			<!-- End of Rating -->
		</div>
		<div class="clear"></div>
	</div><!-- #minor-publishing -->

	<div id="major-publishing-actions" class="awsm-application-major-actions">
		<?php $this->application_delete_action( $application_id ); ?>
		<div class="clear"></div>
	</div><!-- #major-publishing-actions -->

	<?php
		/**
		 * Fires after applicant meta box actions.
		 *
		 * @since 1.6.0
		 */
		do_action( 'after_awsm_job_applicant_mb_actions', $application_id );
	?>
</div>
