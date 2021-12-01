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

	if ( ! empty( $jobs ) ) :
		?>
			<table class="awsm-jobs-overview-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'wp-job-openings' ); ?></th>
						<th><?php esc_html_e( 'Position', 'wp-job-openings' ); ?></th>
					<?php
					if ( current_user_can( 'edit_applications' ) ) {
						printf( '<th>%s</th>', esc_html__( 'Applications', 'wp-job-openings' ) );
					}
					?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $jobs as $job ) :
						$job_title = get_the_title( $job->ID );
						?>
						<tr>
							<td><?php echo esc_html( $job->ID ); ?></td>
							<td>
							<?php
							if ( current_user_can( 'edit_post', $job->ID ) ) {
								printf( '<a href="%2$s">%1$s</a>', esc_html( $job_title ), esc_url( get_edit_post_link( $job->ID ) ) );
							} else {
								echo esc_html( $job_title );
							}
							?>
							</td>
							<?php
							if ( current_user_can( 'edit_applications' ) ) {
								if ( $job->applications_count > 0 ) {
									printf( '<td><a href="%2$s">%1$s</a></td>', esc_html( $job->applications_count ), esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $job->ID ) ) );
								} else {
									printf( '<td>%s</td>', esc_html( $job->applications_count ) );
								}
							}
							?>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings' ) ); ?>"><?php esc_html_e( 'View All →', 'wp-job-openings' ); ?></a>
						</td>
					</tr>
				</tfoot>
			</table>
	<?php else : ?>
			<div class="awsm-jobs-overview-empty-wrapper">
				<p>💼
				<?php
					/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
					printf( '&nbsp;' . esc_html__( 'Looks empty! %1$sAdd some%2$s', 'wp-job-openings' ), '<a href="' . esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ) . '">', '</a>' );
				?>
				</p>
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
