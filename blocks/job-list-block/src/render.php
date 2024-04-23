<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
if (!defined('AWSM_JOBS_LISTING_PLUGIN_DIR')) {
	define('AWSM_JOBS_LISTING_PLUGIN_DIR', 'wp-content/plugins/wp-job-openings ');
}


$job_posts = array(
	'posts_per_page' => $attributes['listing_per_page'],
	'post_type' => 'awsm_job_openings',
	'post_status' => array('publish')
);

$jobs = get_posts($job_posts);

?>
<div>
	<?php if ($jobs): ?>
		<div class="awsm-job-wrap">
			<?php foreach ($jobs as $job): ?>
				<?php if($attributes['layout']=="grid"):?>
				<div class="awsm-job-listings awsm-row awsm-<?php echo $attributes['layout'] ?>-col-3"
					data-listings="<?php echo $attributes['listing_per_page']; ?>">
					<div class="awsm-job-<?php echo $attributes['layout']; ?>-item awsm-<?php echo $attributes['layout'] ?>-item"
						id="awsm-<?php echo $attributes['layout'] ?>-item-<?php echo $job->ID ?>">

						<a href="<?php echo esc_url(get_permalink($job->ID)); ?>" class="awsm-job-item">
							<div class="awsm-<?php echo $attributes['layout'] ?>-left-col">

								<h2 class="awsm-job-post-title">
									<?php $job_title = get_the_title($job->ID); ?>
									<?php echo esc_html($job_title); ?>
								</h2>
							</div>
							<div class="awsm-<?php echo $attributes['layout']; ?>-right-col">
								<div class="awsm-job-specification-wrapper">
									<?php
									$post_id = $job->ID;
									// Array of taxonomy names
									$taxonomy_names = array('job-location', 'job-category');

									// Initialize an empty array to store all term taxonomies
									$all_term_taxonomies = array();

									// Loop through each taxonomy
									foreach ($taxonomy_names as $taxonomy_name) {
										// Get terms for the current taxonomy
										$term_taxonomies = wp_get_post_terms($post_id, $taxonomy_name, array('fields' => 'all'));

										// Merge the term taxonomies into the main array
										$all_term_taxonomies = array_merge($all_term_taxonomies, $term_taxonomies);
									}

									// Loop through the combined array of term taxonomies and display information
									if (!empty($all_term_taxonomies) && !is_wp_error($all_term_taxonomies)) {
										foreach ($all_term_taxonomies as $term_taxonomy) {
											// Output term taxonomy information
							
											echo '	<div class="awsm-job-specification-item awsm-job-specification-job-category">
		<span class="awsm-job-specification-term"> ' . $term_taxonomy->name . '</span>
		</div>';

										}
									} ?>
								</div>
								<div class="awsm-job-more-container"><span class="awsm-job-more">More Details </span>
								</div>
							</div>
						</a>
					</div>
				</div>

			</div>
			<?php else:?>
				<div class="awsm-job-wrap">

		
		<div class="awsm-job-listings awsm-lists" data-listings="10">
			<div class="awsm-job-listing-item awsm-list-item" id="awsm-list-item-10">		<div class="awsm-job-item">			<div class="awsm-list-left-col">
				
				
				<h2 class="awsm-job-post-title">
					<a href="<?php echo esc_url(get_permalink($job->ID)); ?>"><?php $job_title = get_the_title($job->ID); ?>
									<?php echo esc_html($job_title); ?></a>				</h2>

							</div>

			<div class="awsm-list-right-col">
				<div class="awsm-job-specification-wrapper"><?php
									$post_id = $job->ID;
									// Array of taxonomy names
									$taxonomy_names = array('job-location', 'job-category');

									// Initialize an empty array to store all term taxonomies
									$all_term_taxonomies = array();

									// Loop through each taxonomy
									foreach ($taxonomy_names as $taxonomy_name) {
										// Get terms for the current taxonomy
										$term_taxonomies = wp_get_post_terms($post_id, $taxonomy_name, array('fields' => 'all'));

										// Merge the term taxonomies into the main array
										$all_term_taxonomies = array_merge($all_term_taxonomies, $term_taxonomies);
									}

									// Loop through the combined array of term taxonomies and display information
									if (!empty($all_term_taxonomies) && !is_wp_error($all_term_taxonomies)) {
										foreach ($all_term_taxonomies as $term_taxonomy) {
											// Output term taxonomy information
							
											echo '	<div class="awsm-job-specification-item awsm-job-specification-job-category">
		<span class="awsm-job-specification-term"> ' . $term_taxonomy->name . '</span>
		</div>';

										}
									} ?>
								</div>
								<div class="awsm-job-more-container"><a class="awsm-job-more" href="<?php echo esc_url(get_permalink($job->ID)); ?>">More Details </a>
								</div>
							</div>
		</div>	</div>		</div>

	</div>

			<?php endif;?>

		<?php endforeach; ?>

	<?php else: ?>
		<div class="jobs-none-container">
			<p><?php awsm_no_jobs_msg(); ?></p>
		</div>
	<?php endif; ?>
</div>