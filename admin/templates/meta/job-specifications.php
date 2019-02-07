<?php
	if( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	$awsm_filters = get_option( 'awsm_jobs_filter' );
	$taxonomy_objects = get_object_taxonomies( 'awsm_job_openings', 'objects' );
?>

<div class="awsm-job-specifications-section" id="awsm_job_specifications">
	<?php
		if( ! empty( $taxonomy_objects ) && ! empty( $awsm_filters ) ) :
			$spec_keys = wp_list_pluck( $awsm_filters, 'taxonomy' );
			
			echo '<ul class="awsm-job-specification-wrapper">';
			foreach( $taxonomy_objects as $taxonomy => $taxonomy_options ) :
				if ( ! in_array( $taxonomy, $spec_keys, true ) ) {
					continue;
				}
				$terms = get_terms( $taxonomy, 'orderby=id&hide_empty=0' );
				$post_terms = get_the_terms( $post->ID, $taxonomy );
				$post_terms_ids = array();
				if( ! empty( $post_terms ) ) {
					foreach( $post_terms as $post_term ) {
						$post_terms_ids[] = $post_term->term_id;
					}
				}
	?>
				<li>
					<input type="hidden" name="awsm_job_spec_terms[<?php echo $taxonomy; ?>][]" value="" />
					<label for="awsm_job_<?php echo $taxonomy; ?>_specification"><?php echo esc_html( $taxonomy_options->label ); ?></label>
					<select class="awsm_job_specification_terms" id="awsm_job_<?php echo $taxonomy; ?>_specification" name="awsm_job_spec_terms[<?php echo $taxonomy; ?>][]" multiple="multiple" style="width: 100%;">
					    <?php
					        if( ! empty( $terms ) ) :
					            foreach( $terms as $term ) :
					    ?>
					                <option value="<?php echo esc_attr( $term->term_id ); ?>"<?php echo ( ! empty( $post_terms_ids ) ) ? ( in_array( $term->term_id, $post_terms_ids ) ? ' selected' : '' ) : ''; ?>><?php echo esc_html( $term->name ); ?></option>
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