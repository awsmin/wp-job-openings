<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$awsm_filters           = get_option( 'awsm_jobs_filter' );
	$taxonomy_objects       = get_object_taxonomies( 'awsm_job_openings', 'objects' );
	$spec_label_placeholder = 'placeholder="' . esc_html__( 'Enter a specification', 'wp-job-openings' ) . '"';
	$spec_tags_placeholder  = 'data-placeholder="' . esc_html__( 'Enter options', 'wp-job-openings' ) . '"';
	$icon_placeholder       = 'data-placeholder="' . esc_html__( 'Select icon', 'wp-job-openings' ) . '"';
?>

<div id="settings-awsm-settings-specifications" class="awsm-admin-settings">
	<?php do_action( 'awsm_settings_form_elem_start', 'specifications' ); ?>
	<form method="POST" action="options.php" id="awsm-job-specifications-form">
		<?php
			settings_fields( 'awsm-jobs-specifications-settings' );

			// display specifications subtabs.
			$this->display_subtabs( 'specifications' );

			do_action( 'before_awsm_settings_main_content', 'specifications' );
		?>
		<div class="awsm-form-section-main awsm-sub-options-container" id="awsm-job-specifications-options-container">
			<div class="awsm-form-section">
				<table id="awsm-repeatable-specifications" width="100%" class="awsm-specs" data-next="<?php echo ( ! empty( $awsm_filters ) ) ? count( $awsm_filters ) : 1; ?>">
					<thead>
						 <tr>
						   <th scope="row" colspan="6" class="awsm-form-head-title">
								<h2><?php esc_html_e( 'Manage Job Specifications', 'wp-job-openings' ); ?></h2>
							</th>
						</tr>
					</thead>
					<tbody class="awsm_job_specifications_settings_body">
						<?php do_action( 'before_awsm_specifications_settings' ); ?>
						<tr>
							<td class="awsm-specs-drag-control-wrap"></td>
							<td><?php esc_html_e( 'Specifications', 'wp-job-openings' ); ?></td>
							<td><?php esc_html_e( 'Key', 'wp-job-openings' ); ?></td>
							<td><?php esc_html_e( 'Icon (Optional)', 'wp-job-openings' ); ?></td>
							<td><?php esc_html_e( 'Options', 'wp-job-openings' ); ?></td>
							<td></td>
						</tr>
						<?php
							$index = 0;
						if ( empty( $taxonomy_objects ) || empty( $awsm_filters ) ) {
							$this->spec_template( $index );
						} else {
							$spec_keys = wp_list_pluck( $awsm_filters, 'taxonomy' );
							foreach ( $taxonomy_objects as $spec => $spec_options ) {
								if ( ! in_array( $spec, $spec_keys, true ) ) {
									continue;
								}

								$this->spec_template(
									$index,
									array(
										'key'     => $spec,
										'options' => $spec_options,
									),
									$awsm_filters
								);

								$index++;
							}
						}
						?>
						<?php do_action( 'after_awsm_specifications_settings' ); ?>
					</tbody>
				</table>

				<!-- job-spec-templates -->
				<script type="text/html" id="tmpl-awsm-job-spec-settings">
					<?php $this->spec_template( '{{data.index}}' ); ?>
				</script>

				<script type="text/html" id="tmpl-awsm-job-spec-settings-error">
					<div class="awsm-jobs-error-container">
						<div class="awsm-jobs-error">
							<p>
								<strong>
									<# if( data.isInvalidKey ) { #>
										<?php
											esc_html_e( 'The job specification key should only contain alphanumeric, latin characters separated by hyphen/underscore, and cannot begin or end with a hyphen/underscore.', 'wp-job-openings' );
										?>
									<# } #>
								</strong>
							</p>
						</div>
					</div>
				</script>
				<!-- /job-spec-templates -->

				<p><a class="button awsm-add-filter-row" href="#"><?php esc_html_e( 'Add new spec', 'wp-job-openings' ); ?></a></p>
			</div><!-- .awsm-form-section -->
		</div><!-- .awsm-form-section-main -->

		<?php do_action( 'after_awsm_settings_main_content', 'specifications' ); ?>

		<div class="awsm-form-footer">
			<?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'specifications' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div><!-- .awsm-form-footer -->
	</form>
	<?php do_action( 'awsm_settings_form_elem_end', 'specifications' ); ?>
</div><!-- .awsm-admin-settings -->
