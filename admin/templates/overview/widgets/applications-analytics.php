<div class="awsm-jobs-overview-widget-wrapper">
	<?php
		/**
		 * Fires before the overview widget content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $widget_id Overview widget ID.
		 */
		do_action( 'before_awsm_jobs_overview_widget_content', $widget_id );
	?>

	<div class="awsm-jobs-overview-chart-wrapper">
		<canvas id="awsm-jobs-overview-applications-analytics-chart"></canvas>
	</div>
	<div class="awsm-jobs-overview-empty-wrapper awsm-hide">
		<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/applications-analytics-chart.png' ); ?>">
		<p>📂 <?php esc_html_e( 'Awaiting applications', 'wp-job-openings' ); ?></p>
	</div>

	<?php
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
