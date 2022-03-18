<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$awsm_filters     = get_option( 'awsm_jobs_filter' );
	$taxonomy_objects = get_object_taxonomies( 'awsm_job_openings', 'objects' );

	/**
	 * Initialize job specifications meta box.
	 *
	 * @since 1.6.0
	 */
	do_action( 'awsm_job_specs_mb_init', $post->ID );
?>

<div class="awsm-job-specifications-section" id="awsm_job_specifications">
	<?php
	/**
	 * Fires before job specifications meta box content.
	 *
	 * @since 1.6.0
	 */
	do_action( 'before_awsm_job_specs_mb_content', $post->ID );

	if ( ! empty( $taxonomy_objects ) && ! empty( $awsm_filters ) ) :
		$spec_keys = wp_list_pluck( $awsm_filters, 'taxonomy' );

		echo '<ul class="awsm-job-specification-wrapper">';
		foreach ( $taxonomy_objects as $spec => $spec_options ) :
			if ( ! in_array( $spec, $spec_keys, true ) ) {
				continue;
			}

			/**
			 * Filter the arguments for specification terms.
			 *
			 * @since 3.3.0
			 *
			 * @param array $terms_args Array of arguments.
			 */
			$terms_args = apply_filters(
				'awsm_jobs_spec_terms_args',
				array(
					'taxonomy'   => $spec,
					'orderby'    => 'name',
					'hide_empty' => false,
				)
			);
			$spec_terms = get_terms( $terms_args );

			$post_terms     = get_the_terms( $post->ID, $spec );
			$post_terms_ids = array();
			if ( ! empty( $post_terms ) ) {
				foreach ( $post_terms as $post_term ) {
					$post_terms_ids[] = $post_term->term_id;
				}
			}
			?>
				<li>
					<input type="hidden" name="awsm_job_spec_terms[<?php echo esc_attr( $spec ); ?>][]" value="" />
					<label for="awsm_job_<?php echo esc_attr( $spec ); ?>_specification"><?php echo esc_html( $spec_options->label ); ?></label>
					<select class="awsm_job_specification_terms" id="awsm_job_<?php echo esc_attr( $spec ); ?>_specification" name="awsm_job_spec_terms[<?php echo esc_attr( $spec ); ?>][]" multiple="multiple" style="width: 100%;">
					<?php
					if ( ! empty( $spec_terms ) ) :
						foreach ( $spec_terms as $spec_term ) :
							?>
								<option value="<?php echo esc_attr( $spec_term->term_id ); ?>"<?php echo ( ! empty( $post_terms_ids ) ) ? ( in_array( $spec_term->term_id, $post_terms_ids ) ? ' selected' : '' ) : ''; ?>><?php echo esc_html( $spec_term->name ); ?></option>
							<?php
							endforeach;
							endif;
					?>
					</select>
				</li>
			<?php
			endforeach;
		echo '</ul><!-- .awsm-job-specification-wrapper -->';
	endif;

	/**
	 * Fires after job specifications meta box content.
	 *
	 * @since 1.6.0
	 */
	do_action( 'after_awsm_job_specs_mb_content', $post->ID );
	?>
</div><!-- #awsm_job_specifications -->
