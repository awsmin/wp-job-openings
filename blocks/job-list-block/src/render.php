<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
if (!defined('AWSM_JOBS_LISTING_PLUGIN_DIR')) {
	define('AWSM_JOBS_LISTING_PLUGIN_DIR', 'wp-content/plugins/wp-job-openings ');
}
if(!function_exists('get_wpjo_filter')){
function get_wpjo_filter($args){
	$filter_form =new AWSM_Job_Openings_Filters();
	//echo $filter_form->display_filter_form($args);
}
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
		
		<?php 
			if($attributes['show_filter_flag']==1):
				do_action( 'awsm_filter_form' );
			endif;
			$query = new WP_Query($job_posts); ?>
<div class="awsm-job-listings awsm-row awsm-grid-col-3" data-listings="3" data-specs="|orderby:rand(1221638413943695184) DESC">
			<?php
			if ( $query->have_posts() ) {
				include AWSM_Job_Openings::get_template_path( 'main.php', 'job-openings' );
			}
		?>
			</div>

	<?php else: ?>
		<div class="jobs-none-container">
			<p><?php awsm_no_jobs_msg(); ?></p>
		</div>
	<?php endif; ?>
</div>