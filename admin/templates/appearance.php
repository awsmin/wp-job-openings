<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$no_columns_choices  = $job_specs_choices = $available_filters_choices = $listing_specs_choices = array(); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
	$listing_view        = get_option( 'awsm_jobs_listing_view' );
	$specifications      = get_option( 'awsm_jobs_filter' );
	$hidden_class        = 'awsm-hide';
	$enable_filters      = get_option( 'awsm_enable_job_filter_listing' );
	$no_columns_options  = apply_filters( 'awsm_jobs_number_of_columns_options', array( 1, 2, 3, 4 ) );
	$job_specs_positions = apply_filters(
		'awsm_jobs_specifications_position',
		array(
			'below_content' => 'Below job description',
			'above_content' => 'Above job description',
		)
	);

	if ( ! empty( $no_columns_options ) ) {
		foreach ( $no_columns_options as $column ) {
			/* translators: %d: number of columns in grid view layout */
			$text                 = sprintf( _n( '%d Column', '%d Columns', $column, 'wp-job-openings' ), $column );
			$no_columns_choices[] = array(
				'value' => $column,
				'text'  => $text,
			);
		}
	}

	if ( ! empty( $specifications ) ) {
		foreach ( $specifications as $spec ) {
			$spec_key                    = $spec['taxonomy'];
			$general_choice              = array(
				'value' => $spec_key,
				'text'  => $spec['filter'],
			);
			$available_filters_choices[] = array_merge(
				$general_choice,
				array(
					'id' => "awsm_jobs_listing_available_filters-{$spec_key}",
				)
			);
			$listing_specs_choices[]     = array_merge(
				$general_choice,
				array(
					'id' => "awsm_jobs_listing_specs-{$spec_key}",
				)
			);
		}
	}

	if ( ! empty( $job_specs_positions ) ) {
		foreach ( $job_specs_positions as $position => $label ) {
			$job_specs_choices[] = array(
				'value' => $position,
				'text'  => $label,
			);
		}
	}

	/**
	 * Filters the appearance settings fields.
	 *
	 * @since 1.4
	 *
	 * @param array $settings_fields Appearance Settings fields
	 */
	$settings_fields = apply_filters(
		'awsm_jobs_appearance_settings_fields',
		array(
			'listing' => array(
				array(
					'id'    => 'awsm-appearance-listing-layout-title',
					'label' => __( 'Job listing layout options', 'wp-job-openings' ),
					'type'  => 'title',
				),
				array(
					'name'          => 'awsm_jobs_archive_page_template',
					'label'         => __( 'Jobs Archive page template', 'wp-job-openings' ),
					'type'          => 'radio',
					'choices'       => array(
						array(
							'value' => 'theme',
							'text'  => __( 'Theme Template', 'wp-job-openings' ),
						),
						array(
							'value' => 'plugin',
							'text'  => __( 'Plugin Template', 'wp-job-openings' ),
						),
					),
					'default_value' => 'plugin',
				),
				array(
					'name'    => 'awsm_jobs_listing_view',
					'label'   => __( 'Layout of job listing page', 'wp-job-openings' ),
					'type'    => 'radio',
					'class'   => 'awsm-check-toggle-control',
					'choices' => array(
						array(
							'value'      => 'list-view',
							'text'       => __( 'List view ', 'wp-job-openings' ),
							'data_attrs' => array(
								array(
									'attr'  => 'toggle-target',
									'value' => '#awsm_jobs_number_of_columns_row',
								),
							),
						),
						array(
							'value'      => 'grid-view',
							'text'       => __( 'Grid view ', 'wp-job-openings' ),
							'data_attrs' => array(
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
					'value'   => $listing_view,
				),
				array(
					'name'            => 'awsm_jobs_number_of_columns',
					'label'           => __( 'Number of columns ', 'wp-job-openings' ),
					'type'            => 'select',
					'container_id'    => 'awsm_jobs_number_of_columns_row',
					'container_class' => $listing_view === 'list-view' ? $hidden_class : '',
					'class'           => 'awsm-select-control regular-text',
					'choices'         => $no_columns_choices,
				),
				array(
					'name'        => 'awsm_jobs_list_per_page',
					'label'       => __( 'Listings per page ', 'wp-job-openings' ),
					'type'        => 'number',
					'other_attrs' => array(
						'min' => '1',
					),
				),
				array(
					'name'          => 'awsm_jobs_pagination_type',
					'label'         => __( 'Pagination Type', 'wp-job-openings' ),
					'type'          => 'radio',
					'choices'       => array(
						array(
							'value' => 'classic',
							'text'  => __( 'Classic', 'wp-job-openings' ),
						),
						array(
							'value' => 'modern',
							'text'  => __( 'Modern', 'wp-job-openings' ),
						),
					),
					'default_value' => 'modern',
				),
				array(
					'id'      => 'awsm-appearance-listing-filter-title',
					'visible' => ! empty( $specifications ),
					'label'   => __( 'Job filter options', 'wp-job-openings' ),
					'type'    => 'title',
				),
				array(
					'name'        => 'awsm_enable_job_search',
					'label'       => __( 'Job Search ', 'wp-job-openings' ),
					'type'        => 'checkbox',
					'choices'     => array(
						array(
							'value' => 'enable',
							'text'  => __( 'Enable job search field in job listing', 'wp-job-openings' ),
						),
					),
					'description' => __( 'Check this option to show job search field in the job listing page', 'wp-job-openings' ),
				),
				array(
					'name'        => 'awsm_enable_job_filter_listing',
					'visible'     => ! empty( $specifications ),
					'label'       => __( 'Job filters', 'wp-job-openings' ),
					'type'        => 'checkbox',
					'class'       => 'awsm-check-toggle-control',
					'choices'     => array(
						array(
							'value'      => 'enabled',
							'text'       => __( 'Enable job filters in job listing ', 'wp-job-openings' ),
							'data_attrs' => array(
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
					'value'       => $enable_filters,
					'description' => __( 'Check this option to show job filter options in the job listing page', 'wp-job-openings' ),
				),
				array(
					'name'            => 'awsm_jobs_listing_available_filters',
					'visible'         => ! empty( $specifications ),
					'label'           => __( 'Available filters', 'wp-job-openings' ),
					'type'            => 'checkbox',
					'multiple'        => true,
					'container_id'    => 'awsm_jobs_available_filters_row',
					'container_class' => $enable_filters !== 'enabled' ? $hidden_class : '',
					'choices'         => $available_filters_choices,
					'description'     => __( 'Check the job specs you want to enable as filters', 'wp-job-openings' ),
				),
				array(
					'id'    => 'awsm-appearance-listing-other-options-title',
					'label' => __( 'Other options', 'wp-job-openings' ),
					'type'  => 'title',
				),
				array(
					'name'        => 'awsm_jobs_listing_specs',
					'visible'     => ! empty( $specifications ),
					'label'       => __( 'Job specs in the listing', 'wp-job-openings' ),
					'type'        => 'checkbox',
					'multiple'    => true,
					'choices'     => $listing_specs_choices,
					'description' => __( 'Check the job specs you want to show along with the listing view', 'wp-job-openings' ),
				),
				array(
					'name'    => 'awsm_jobs_expired_jobs_listings',
					'label'   => __( 'Expired Jobs', 'wp-job-openings' ),
					'type'    => 'checkbox',
					'choices' => array(
						array(
							'value' => 'expired',
							'text'  => __( 'Hide expired jobs from listing page', 'wp-job-openings' ),
						),
					),
				),
			),
			'details' => array(
				array(
					'id'    => 'awsm-appearance-detail-page-layout-title',
					'label' => __( 'Job detail page layout options', 'wp-job-openings' ),
					'type'  => 'title',
				),
				array(
					'name'          => 'awsm_jobs_details_page_template',
					'label'         => __( 'Job detail page template', 'wp-job-openings' ),
					'type'          => 'radio',
					'choices'       => array(
						array(
							'value' => 'default',
							'text'  => __( 'Theme Template', 'wp-job-openings' ),
						),
						array(
							'value' => 'custom',
							'text'  => __( 'Plugin Template', 'wp-job-openings' ),
						),
					),
					'default_value' => 'default',
				),
				array(
					'name'    => 'awsm_jobs_details_page_layout',
					'label'   => __( 'Layout of job detail page', 'wp-job-openings' ),
					'type'    => 'radio',
					'choices' => array(
						array(
							'value' => 'single',
							'text'  => __( 'Single Column ', 'wp-job-openings' ),
						),
						array(
							'value' => 'two',
							'text'  => __( 'Two Columns ', 'wp-job-openings' ),
						),
					),
				),
				array(
					'id'       => 'awsm-appearance-detail-page-specifications-group',
					'label'    => __( 'Job specifications', 'wp-job-openings' ),
					'type'     => 'checkbox',
					'multiple' => true,
					'choices'  => array(
						array(
							'value'         => 'show_in_detail',
							'name'          => 'awsm_jobs_specification_job_detail',
							'text'          => __( 'Show job specifications in job detail page', 'wp-job-openings' ),
							'default_value' => 'show_in_detail',
						),
						array(
							'value'         => 'show_icon',
							'name'          => 'awsm_jobs_show_specs_icon',
							'text'          => __( 'Show icons for job specifications in job detail page', 'wp-job-openings' ),
							'default_value' => 'show_icon',
						),
						array(
							'value' => 'make_clickable',
							'name'  => 'awsm_jobs_make_specs_clickable',
							'text'  => __( 'Make job specifications clickable in job detail page', 'wp-job-openings' ),
						),
					),
				),
				array(
					'name'          => 'awsm_jobs_specs_position',
					'label'         => __( 'Job spec position ', 'wp-job-openings' ),
					'type'          => 'select',
					'class'         => 'awsm-select-control regular-text',
					'choices'       => $job_specs_choices,
					'default_value' => 'below_content',
				),
				array(
					'id'       => 'awsm-appearance-detail-page-other-options-group',
					'label'    => __( 'Other display options', 'wp-job-openings' ),
					'type'     => 'checkbox',
					'multiple' => true,
					'choices'  => array(
						array(
							'value' => 'content',
							'name'  => 'awsm_jobs_expired_jobs_content_details',
							'text'  => __( 'Hide content of expired listing from job detail page ', 'wp-job-openings' ),
						),
						array(
							'value' => 'block_expired',
							'name'  => 'awsm_jobs_expired_jobs_block_search',
							'text'  => __( 'Block search engine robots to expired jobs', 'wp-job-openings' ),
						),
						array(
							'value' => 'hide_date',
							'name'  => 'awsm_jobs_hide_expiry_date',
							'text'  => __( 'Hide expiry date from job detail page', 'wp-job-openings' ),
						),
					),
				),
			),
		)
	);
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
					<?php
						do_action( 'before_awsm_appearance_details_settings' );

						$this->display_settings_fields( $settings_fields['details'] );

						do_action( 'after_awsm_appearance_details_settings' );
					?>
				</tbody>
			</table>
		</div><!-- #awsm-job-details-options-container -->

		<?php do_action( 'after_awsm_settings_main_content', 'appearance' ); ?>

		<div class="awsm-form-footer">
			<?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'appearance' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div><!-- .awsm-form-footer -->
	</form>
	<?php do_action( 'awsm_settings_form_elem_end', 'appearance' ); ?>
</div><!-- .awsm-admin-settings -->
