<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$awsm_filters     = get_option( 'awsm_jobs_filter' );
	$taxonomy_objects = get_object_taxonomies( 'awsm_job_openings', 'objects' );
?>

<div class="awsm-job-specifications-section" id="awsm_job_specifications">
	<?php
	if ( ! empty( $taxonomy_objects ) && ! empty( $awsm_filters ) ) :
		$spec_keys = wp_list_pluck( $awsm_filters, 'taxonomy' );

		echo '<ul class="awsm-job-specification-wrapper">';
		foreach ( $taxonomy_objects as $spec => $spec_options ) :
			if ( ! in_array( $spec, $spec_keys, true ) ) {
				continue;
			}
			$spec_terms     = get_terms( $spec, 'orderby=id&hide_empty=0' );
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
	?>
</div><!-- #awsm_job_specifications -->
