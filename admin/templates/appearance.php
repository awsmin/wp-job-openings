<?php

    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    $listing_view = get_option( 'awsm_jobs_listing_view' );
    $list_page = get_option( 'awsm_jobs_list_per_page' );
    $number_list_columns = get_option( 'awsm_jobs_number_of_columns' );
    $specifications = get_option( 'awsm_jobs_filter' );
    $hidden_class = 'awsm-hide';
    $enable_filters = get_option( 'awsm_enable_job_filter_listing' );
    $spec_position = get_option( 'awsm_jobs_specs_position', 'below_content' );
    $job_specs_positions = apply_filters( 'awsm_jobs_specifications_position', array(
        'below_content'    => 'Below job description',
        'above_content'    => 'Above job description'
    ) );

    $no_columns_options = apply_filters( 'awsm_jobs_number_of_columns_options', array( 1, 2, 3, 4 ) );
    $no_columns_choices = array();
    if( ! empty( $no_columns_options ) ) {
        foreach( $no_columns_options as $column ) {
            $text = sprintf( _n( '%d Column', '%d Columns', $column, 'wp-job-openings' ), $column );
            $no_columns_choices[] = array(
                'value'       => $column,
                'text'        => $text,
            );
        }
    }

    $available_filters_choices = $listing_specs_choices = array();
    if( ! empty( $specifications ) ) {
        foreach ( $specifications as $spec ) {
            $general_choice = array(
                'value'       => $spec['taxonomy'],
                'text'        => $spec['filter'],
            );
            $available_filters_choices[] = array_merge( $general_choice, array(
                'id'          => 'awsm_jobs_listing_available_filters' . '-' . $spec['taxonomy']
            ) );
            $listing_specs_choices[] = array_merge( $general_choice, array(
                'id'          => 'awsm_jobs_listing_specs' . '-' . $spec['taxonomy']
            ) );
        }
    }

    /**
     * Filters the appearance settings fields.
     *
     * @since 1.4
     * 
     * @param array $settings_fields Appearance Settings fields
     */
    $settings_fields = apply_filters( 'awsm_jobs_appearance_settings_fields', array(
        'listing' => array(
            'job_listing_layout_title' => array(
                'label'        => __( 'Job listing layout options', 'wp-job-openings' ),
                'type'         => 'title',
            ),
            'awsm_jobs_listing_view' => array(
                'label'        => __( 'Layout of job listing page', 'wp-job-openings' ),
                'type'         => 'radio',
                'class'        => 'awsm-check-toggle-control',
                'choices'      => array( 
                    array(
                        'value'       => 'list-view',
                        'text'        => __( 'List view ', 'wp-job-openings' ),
                        'id'          => 'awsm-list-view',
                        'data_attrs'  => array(
                            array(
                                'attr'  => 'toggle-target',
                                'value' => '#awsm_jobs_number_of_columns_row',
                            ),
                        ),
                    ),
                    array(
                        'value'       => 'grid-view',
                        'text'        => __( 'Grid view ', 'wp-job-openings' ),
                        'id'          => 'awsm-grid-view',
                        'data_attrs'  => array(
                            array(
                                'attr'  => 'toggle',
                                'value' => 'true',
                            ),
                            array(
                                'attr'  => 'toggle-target',
                                'value' => '#awsm_jobs_number_of_columns_row',
                            ),
                        ),
                    ),
                ),
                'value'        => $listing_view,
            ),
            'awsm_jobs_number_of_columns' => array(
                'label'           => __( 'Number of columns ', 'wp-job-openings' ),
                'type'            => 'select',
                'container_id'    => 'awsm_jobs_number_of_columns_row',
                'container_class' => $listing_view === 'list-view' ? $hidden_class : '',
                'class'           => 'awsm-select-control regular-text',
                'choices'         => $no_columns_choices,
                'value'           => get_option( 'awsm_jobs_number_of_columns' ),
            ),
            'awsm_jobs_list_per_page' => array(
                'label'           => __( 'Listings per page ', 'wp-job-openings' ),
                'type'            => 'number',
                'value'           => get_option( 'awsm_jobs_list_per_page' ),
                'other_attrs'     => array(
                    'min' => "1",
                ),
            ),
            'job_filter_title' => array(
                'visible'      => ! empty( $specifications ),
                'label'        => __( 'Job filter options', 'wp-job-openings' ),
                'type'         => 'title',
            ),
            'awsm_enable_job_filter_listing' => array(
                'visible'      => ! empty( $specifications ),
                'label'        => __( 'Job filters', 'wp-job-openings' ),
                'type'         => 'checkbox',
                'class'        => 'awsm-check-toggle-control',
                'choices'      => array( 
                    array(
                        'value'       => 'enabled',
                        'text'        => __( 'Enable job filters in job listing ', 'wp-job-openings' ),
                        'data_attrs'  => array(
                            array(
                                'attr'  => 'toggle',
                                'value' => 'true',
                            ),
                            array(
                                'attr'  => 'toggle-target',
                                'value' => '#awsm_jobs_available_filters_row',
                            ),
                        ),
                    ),
                ),
                'value'        => $enable_filters,
                'description'  => __( 'Check this option to show job filter options in the job listing page', 'wp-job-openings' ),
            ),
            'awsm_jobs_listing_available_filters' => array(
                'visible'      => ! empty( $specifications ),
                'label'           => __( 'Available filters', 'wp-job-openings' ),
                'type'            => 'checkbox',
                'multiple'        => true,
                'container_id'    => 'awsm_jobs_available_filters_row',
                'container_class' => $enable_filters !== 'enabled' ? $hidden_class : '',
                'class'           => '',
                'choices'         => $available_filters_choices,
                'value'           => get_option( 'awsm_jobs_listing_available_filters' ),
                'description'     => __( 'Check the job specs you want to enable as filters', 'wp-job-openings' ),
            ),
            'other_options_title' => array(
                'label'        => __( 'Other options', 'wp-job-openings' ),
                'type'         => 'title',
            ),
            'awsm_jobs_listing_specs' => array(
                'visible'         => ! empty( $specifications ),
                'label'           => __( 'Job specs in the listing', 'wp-job-openings' ),
                'type'            => 'checkbox',
                'multiple'        => true,
                'class'           => '',
                'choices'         => $listing_specs_choices,
                'value'           => get_option( 'awsm_jobs_listing_specs' ),
                'description'     => __( 'Check the job specs you want to show along with the listing view', 'wp-job-openings' ),
            ),
            'awsm_jobs_expired_jobs_listings' => array(
                'label'        => __( 'Expired Jobs', 'wp-job-openings' ),
                'type'         => 'checkbox',
                'class'        => '',
                'id'           => 'awsm-hide-jobs',
                'choices'      => array( 
                    array(
                        'value'       => 'expired',
                        'text'        => __( 'Hide expired jobs from listing page', 'wp-job-openings' ),
                    ),
                ),
                'value'        => get_option( 'awsm_jobs_expired_jobs_listings' ),
            )
        ),
        'detail' => array(
            'job_detail_layout_title' => array(
                'label'        => __( 'Job detail page layout options', 'wp-job-openings' ),
                'type'         => 'title',
            ),
            'awsm_jobs_details_page_template' => array(
                'label'        => __( 'Job detail page template', 'wp-job-openings' ),
                'type'         => 'radio',
                'class'        => '',
                'choices'      => array( 
                    array(
                        'value'       => 'default',
                        'text'        => __( 'Theme Template', 'wp-job-openings' ),
                        'id'          => 'awsm_jobs_default_template',
                    ),
                    array(
                        'value'       => 'custom',
                        'text'        => __( 'Plugin Template', 'wp-job-openings' ),
                        'id'          => 'awsm_jobs_custom_template',
                    ),
                ),
                'value'        => get_option( 'awsm_jobs_details_page_template', 'default' ),
            ),
            'awsm_jobs_details_page_layout' => array(
                'label'        => __( 'Layout of job detail page', 'wp-job-openings' ),
                'type'         => 'radio',
                'class'        => '',
                'choices'      => array( 
                    array(
                        'value'       => 'single',
                        'text'        => __( 'Single Column ', 'wp-job-openings' ),
                        'id'          => 'awsm-job-single-layout',
                    ),
                    array(
                        'value'       => 'two',
                        'text'        => __( 'Two Columns ', 'wp-job-openings' ),
                        'id'          => 'awsm-job-two-columns-layout',
                    ),
                ),
                'value'        => get_option( 'awsm_jobs_details_page_layout' ),
            ),
        ),
    ) );
?>

<div id="settings-awsm-settings-appearance" class="awsm-admin-settings">
    <?php do_action( 'awsm_settings_form_elem_start', 'appearance' ); ?>
    <form method="POST" action="options.php" id="appearance_options_form">

        <?php
            settings_fields( 'awsm-jobs-appearance-settings' );
            
            // display form subtabs.
            $this->display_subtabs( 'appearance' );

            do_action( 'before_awsm_settings_main_content', 'appearance' );
        ?>

        <div class="awsm-form-section-main awsm-sub-options-container" id="awsm-job-listing-options-container">
            <table class="form-table">
                <tbody>
                    <?php
                        do_action( 'before_awsm_appearance_listing_settings' );

                        $this->display_settings_fields( $settings_fields['listing'] );

                        do_action( 'after_awsm_appearance_listing_settings' );
                    ?>
                </tbody>
            </table>
        </div><!-- #awsm-job-listing-options-container -->

        <div class="awsm-form-section-main awsm-sub-options-container" id="awsm-job-details-options-container" style="display: none;">
            <table class="form-table">
                <tbody>
                    <?php do_action( 'before_awsm_appearance_details_settings' ); ?>

                    <?php $this->display_settings_fields( $settings_fields['detail'] ); ?>

                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Job specifications', 'wp-job-openings' ); ?>
                        </th>
                        <td>
                            <ul class="awsm-check-list">
                                <li>
                                    <label for="awsm_jobs_specification_job_detail">
                                        <input type="checkbox" id="awsm_jobs_specification_job_detail"  name="awsm_jobs_specification_job_detail" value="show_in_detail" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_specification_job_detail', 'show_in_detail' ), 'show_in_detail' ) ); ?> /><?php esc_html_e( 'Show job specifications in job detail page', 'wp-job-openings' ); ?>
                                    </label>
                                </li>
                                <li>
                                    <label for="awsm_jobs_show_specs_icon">
                                        <input type="checkbox" id="awsm_jobs_show_specs_icon" name="awsm_jobs_show_specs_icon" value="show_icon" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_show_specs_icon', 'show_icon' ), 'show_icon' ) ); ?> /><?php esc_html_e( 'Show icons for job specifications in job detail page', 'wp-job-openings' ); ?>
                                    </label>
                                </li>
                                <li>
                                    <label for="awsm_jobs_make_specs_clickable">
                                        <input type="checkbox" name="awsm_jobs_make_specs_clickable" id="awsm_jobs_make_specs_clickable" value="make_clickable" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_make_specs_clickable' ), 'make_clickable' ) ); ?> /><?php esc_html_e( 'Make job specifications clickable in job detail page', 'wp-job-openings' ); ?>
                                    </label>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr id="awsm_jobs_specification_position">
                        <th scope="row">
                            <label for="awsm_jobs_specs_position"><?php esc_html_e( 'Job spec position ', 'wp-job-openings' ); ?></label>
                        </th>
                        <td>
                            <select name="awsm_jobs_specs_position" class="awsm-select-control regular-text" id="awsm_jobs_specs_position">
                                <?php
                                    if( ! empty( $job_specs_positions ) ) {
                                        foreach( $job_specs_positions as $position => $label ) {
                                            $selected = '';
                                            if( $spec_position == $position ) {
                                                $selected = ' selected';
                                            }
                                            printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $position ), esc_html( $label ), $selected );
                                        }
                                    }
                                ?>

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Other display options', 'wp-job-openings' ); ?>
                        </th>
                        <td>
                            <ul class="awsm-check-list">
                                <li>
                                    <label for="awsm-hide-content">
                                        <input type="checkbox" id="awsm-hide-content"  name="awsm_jobs_expired_jobs_content_details" value="content" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_expired_jobs_content_details' ), 'content' ) ); ?> /><?php esc_html_e( 'Hide content of expired listing from job detail page ', 'wp-job-openings' ); ?>
                                    </label>
                                </li>
                                <li>
                                    <label for="awsm_jobs_expired_jobs_block_search">
                                        <input type="checkbox" id="awsm_jobs_expired_jobs_block_search"  name="awsm_jobs_expired_jobs_block_search" value="block_expired" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_expired_jobs_block_search' ), 'block_expired' ) ); ?> /><?php esc_html_e( 'Block search engine robots to expired jobs', 'wp-job-openings' ); ?>
                                    </label>
                                </li>
                                <li>
                                    <label for="awsm_jobs_hide_expiry_date">
                                        <input type="checkbox" id="awsm_jobs_hide_expiry_date"  name="awsm_jobs_hide_expiry_date" value="hide_date" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_hide_expiry_date' ), 'hide_date' ) ); ?> /><?php esc_html_e( 'Hide expiry date from job detail page', 'wp-job-openings' ); ?>
                                    </label>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <?php do_action( 'after_awsm_appearance_details_settings' ); ?>
                </tbody>
            </table>
        </div><!-- #awsm-job-details-options-container -->

        <?php do_action( 'after_awsm_settings_main_content', 'appearance' ); ?>

        <div class="awsm-form-footer">
            <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'appearance' ); ?>
        </div><!-- .awsm-form-footer -->
    </form>
    <?php do_action( 'awsm_settings_form_elem_end', 'appearance' ); ?>
</div><!-- .awsm-admin-settings -->