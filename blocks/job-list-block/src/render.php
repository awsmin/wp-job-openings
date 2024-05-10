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
$shortcode_atts = isset( $attributes ) ? $attributes : array();  
$view           = $shortcode_atts['layout'];  
$awsm_filters   = get_option( 'awsm_jobs_filter' );
$listing_specs  = get_option( 'awsm_jobs_listing_specs' ); 

?>

<div class="awsm-job-wrap">
	<?php 
		if($attributes['show_filter_flag']==1):
			do_action( 'awsm_filter_form' );
		endif;
		$query = new WP_Query($job_posts); 
	?>
	<div class="awsm-job-listings awsm-row awsm-<?php echo $attributes['layout'] ?>-col-3" data-listings="<?php echo $attributes['listing_per_page']; ?>">
	<?php 
	include AWSM_Job_Openings::get_template_path( 'main.php', 'job-openings' ); 
	?>
	</div>
</div>
