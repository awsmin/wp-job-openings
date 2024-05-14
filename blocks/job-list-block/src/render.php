<?php 
print_r($attributes);
/* $job_posts = array(
	'posts_per_page' => $attributes['listing_per_page'],
	'post_type' => 'awsm_job_openings',
	'post_status' => array('publish')
);
$jobs = get_posts($job_posts);
$shortcode_atts = isset( $attributes ) ? $attributes : array();  
$view           = $shortcode_atts['layout'];  
$awsm_filters   = get_option( 'awsm_jobs_filter' );
$listing_specs  = get_option( 'awsm_jobs_listing_specs' );  */
?>

<!-- <div class="awsm-job-wrap">
	<?php  
		//do_action( 'awsm_filter_form' );
		//$query = new WP_Query($job_posts); 
	?>
	<div class="awsm-job-listings awsm-row awsm-<?php echo $attributes['layout'] ?>-col-3" data-listings="<?php echo $attributes['listing_per_page']; ?>">
	<?php 
	//include AWSM_Job_Openings::get_template_path( 'main.php', 'job-openings' ); 
	?>
	</div>
</div>
 -->
