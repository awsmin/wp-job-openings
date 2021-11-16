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

		$get_started_links = array(
			array(
				'id'        => 'documentation',
				'url'       => 'https://docs.wpjobopenings.com/',
				'link_text' => __( 'Plugin Documentation', 'wp-job-openings' ),
			),
			array(
				'id'        => 'hooks',
				'url'       => 'https://docs.wpjobopenings.com/developers/hooks',
				'link_text' => __( 'Hooks & Functions', 'wp-job-openings' ),
			),
			array(
				'id'        => 'feedback',
				'url'       => 'https://roadmap.wpjobopenings.com/boards/feedback',
				'link_text' => __( 'Feedback', 'wp-job-openings' ),
			),
			array(
				'id'        => 'roadmap',
				'url'       => 'https://roadmap.wpjobopenings.com/roadmap',
				'link_text' => __( 'Roadmap', 'wp-job-openings' ),
			),
			array(
				'id'        => 'support',
				'url'       => '#',
				'link_text' => __( 'Get Support', 'wp-job-openings' ),
			),
		);
		/**
		 * Filters the overview get started widget links.
		 *
		 * @since 3.0.0
		 *
		 * @param array $get_started_links Links data array.
		 */
		$get_started_links = apply_filters( 'awsm_jobs_overview_get_started_widget_links', $get_started_links );
		?>

	<p><?php esc_html_e( 'Need help with something?', 'wp-job-openings' ); ?></p>

	<ul class="ul-disc">
		<?php
		foreach ( $get_started_links as $link ) {
			printf( '<li><a href="%2$s" target="_blank" rel="noopener">%1$s</a></li>', esc_html( $link['link_text'] ), esc_url( $link['url'] ) );
		}
		?>
	</ul>

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
