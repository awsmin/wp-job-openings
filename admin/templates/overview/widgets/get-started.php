<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="<?php echo esc_attr( "awsm-jobs-overview-widget-wrapper awsm-jobs-overview-{$widget_id}-widget-wrapper" ); ?>">
	<div class="awsm-jobs-overview-widget-get-started">
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
					'url'       => 'https://wordpress.org/support/plugin/wp-job-openings/',
					'link_text' => __( 'Get Support', 'wp-job-openings' ),
				),
			);
			if ( class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
				$support_link_key = array_search( 'support', wp_list_pluck( $get_started_links, 'id' ) );
				if ( $support_link_key !== false ) {
					unset( $get_started_links[ $support_link_key ] );
				}
			}
			/**
			 * Filters the overview get started widget links.
			 *
			 * @since 3.0.0
			 *
			 * @param array $get_started_links Links data array.
			 */
			$get_started_links = apply_filters( 'awsm_jobs_overview_get_started_widget_links', $get_started_links );
			?>
		<div class="awsm-jobs-overview-widget-get-started-content">
			<p><?php esc_html_e( 'Need help with something?', 'wp-job-openings' ); ?></p>

			<ul class="ul-disc">
				<?php
				foreach ( $get_started_links as $gs_link ) {
					printf( '<li><a href="%2$s" target="_blank" rel="noopener">%1$s</a></li>', esc_html( $gs_link['link_text'] ), esc_url( $gs_link['url'] ) );
				}
				?>
			</ul>
		</div><!-- .awsm-jobs-overview-widget-get-started-content -->
		<?php
			printf( '<div class="awsm-jobs-overview-widget-get-started-image"><img src="%1$s"></div>', esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/get-started.svg' ) );
		?>

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
	</div><!-- .awsm-jobs-overview-widget-get-started -->
</div>
