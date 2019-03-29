<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	do_action( 'before_awsm_job_settings_init' );

	$tab_menus   = AWSM_Job_Openings_Settings::settings_tab_menus();
	$current_tab = isset( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : 'general';

?>

<div class="wrap awsm-job-settings-wrap" id="awsm-job-settings-wrap">
	<h1><?php esc_html_e( 'Settings', 'wp-job-openings' ); ?></h1>
	<?php settings_errors(); ?>
	<h2 class="nav-tab-wrapper awsm-settings-tab-wrapper">
		<?php
			$settings_tabs = apply_filters( 'awsm_jobs_settings_tab_menus', $tab_menus );
		foreach ( $settings_tabs as $key => $tab_name ) {
			$active_tab = ( $current_tab === $key ) ? ' nav-tab-active' : '';
			printf(
				'<a href="%2$s" class="nav-tab%3$s">%1$s</a>',
				esc_html( $tab_name ),
				esc_url(
					add_query_arg(
						array(
							'post_type' => 'awsm_job_openings',
							'page'      => 'awsm-jobs-settings',
							'tab'       => $key,
						),
						admin_url( 'edit.php' )
					)
				),
				esc_attr( $active_tab )
			);
		}
		?>
	</h2>

	<div class="awsm-jobs-settings-section-wrapper">
		<div class="awsm-jobs-settings-loader-container">
			<span class="awsm-jobs-settings-loader"><img src="<?php echo esc_url( admin_url( 'images/spinner-2x.gif' ) ); ?>" width="32" height="32" alt="" /></span>
		</div>

		<div id="awsm-jobs-settings-section" class="awsm-jobs-settings-section" style="visibility: hidden;">
			<?php
				$settings_filename = trailingslashit( plugin_dir_path( __FILE__ ) ) . $current_tab . '.php';
			if ( file_exists( $settings_filename ) ) {
				include_once $settings_filename;
			}
				do_action( 'awsm_jobs_settings_tab_section' );
			?>
		</div>
	</div>
</div>
