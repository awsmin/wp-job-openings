<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    do_action( 'before_awsm_job_settings_init' );
?>

<div class="wrap awsm-job-settings-wrap" id="awsm-job-settings-wrap">
    <?php $tab_menus = AWSM_Job_Openings_Settings::settings_tab_menus(); ?>
    <h1><?php esc_html_e('Settings', 'wp-job-openings'); ?></h1>
    <?php settings_errors(); ?>
    <h2 class="nav-tab-wrapper awsm-settings-tab-wrapper">
        <?php
            $settings_tabs = apply_filters( 'awsm_jobs_settings_tab_menus', $tab_menus );
            $count = 1;
            foreach ( $settings_tabs as $key => $tab_name ) {
                $active_tab = ( 1 === $count ) ? 'nav-tab-active' : '';
                echo '<a href="#settings-awsm-settings-' . sanitize_key($key) . '" class="nav-tab ' . sanitize_html_class( $active_tab ) . ' ">' . esc_attr( $tab_name ) . '</a>';
                $count++;
            }
        ?>
    </h2>

    <?php
        foreach( $tab_menus as $key => $tab_name ) {
            $settings_filename = trailingslashit( plugin_dir_path(__FILE__) ) . $key . '.php';
            if( file_exists( $settings_filename ) ) {
                include_once $settings_filename;
            }
        }
        do_action( 'awsm_jobs_settings_tab_section' );
    ?>
</div>