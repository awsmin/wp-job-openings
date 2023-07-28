<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="<?php echo esc_attr( "awsm-jobs-overview-widget-wrapper awsm-jobs-overview-{$widget_id}-widget-wrapper with-table" ); ?>">
	<?php
		/**
		 * Fires before the overview widget content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $widget_id Overview widget ID.
		 */
		do_action( 'before_awsm_jobs_overview_widget_content', $widget_id );

		$applications = AWSM_Job_Openings::get_recent_applications( 10, false );
	if ( ! empty( $applications ) ) :
		?>
			<table class="awsm-jobs-overview-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Applicant', 'wp-job-openings' ); ?></th>
						<th><?php esc_html_e( 'Position', 'wp-job-openings' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $applications as $application ) :
					$applicant_email = get_post_meta( $application->ID, 'awsm_applicant_email', true );
					$avatar          = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 32 ), $application->ID );
					$edit_link       = AWSM_Job_Openings::get_application_edit_link( $application->ID );
					$submission_time = human_time_diff( get_the_time( 'U', $application->ID ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wp-job-openings' );
					?>
						<tr>
							<td>
								<div class="awsm-jobs-overview-applicant">
							<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<div class="awsm-jobs-overview-applicant-in">
										<a href="<?php echo esc_url( $edit_link ); ?>"><?php echo esc_html( $application->post_title ); ?></a>
										<span><?php echo esc_html( $submission_time ); ?></span>
									</div><!-- .awsm-jobs-overview-applicant-in -->
								</div><!-- .awsm-jobs-overview-applicant -->
							</td>
							<td><?php echo esc_html( get_post_meta( $application->ID, 'awsm_apply_for', true ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>"><?php esc_html_e( 'View All →', 'wp-job-openings' ); ?></a>
						</td>
					</tr>
				</tfoot>
			</table>

	<?php else : ?>
			<div class="awsm-jobs-overview-empty-wrapper">
				<p>📂 <?php esc_html_e( 'Awaiting applications', 'wp-job-openings' ); ?></p>
			</div>
		<?php
		endif;

		/**
		 * Fires after the overview widget content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $widget_id Overview widget ID.
		 */
		do_action( 'after_awsm_jobs_overview_widget_content', $widget_id );
	?>
</div>
