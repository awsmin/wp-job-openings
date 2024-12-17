<?php
/**
 * Template for displaying job openings for block side
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $block_atts_set ) ? $block_atts_set : array();
$query      = awsm_block_jobs_query( $attributes ); 

if ( $query->have_posts() ) : ?>
<?php 
if( $attributes['placement'] == 'top' ){ ?>
<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?>">
<?php
	/**
	 * awsm_block_filter_form hook
	 *
	 * Display filter form for job listings
	 *
	 * @hooked AWSM_Job_Openings_Block::display_block_filter_form()
	 *
	 * @since 3.5.0
	 *
	 * @param array $attributes Attributes array from block.
	 */
	do_action( 'awsm_block_filter_form', $attributes );
	do_action( 'awsm_block_form_outside', $attributes );
?>

<div <?php awsm_block_jobs_view_class( '', $attributes ); ?><?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
	<?php
		include get_awsm_jobs_template_path( 'block-main', 'block-files' );
	?>
</div>
</div>
<?php 
	}else{
?>
<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?> awsm-job-form-plugin-style awsm-job-2-col">
	<div class="awsm-b-filter-wrap awsm-jobs-alerts-on">
		<!-- left side bar  -->
		<?php
			/**
			 * awsm_block_filter_form_slide hook
			 *
			 * Display filter form  in placement slide for job listings
			 *
			 * @hooked AWSM_Job_Openings_Block::display_block_filter_form_slide()
			 *
			 * @since 3.5.0
			 *
			 * @param array $attributes Attributes array from block.
			 */
			do_action( 'awsm_block_filter_form_slide', $attributes );
		?>
		
		<!-- Alert icon  -->
	<!-- 	<div class="awsm-jobs-alerts-widget-wrapper">
			<div class="awsm-jobs-alerts-widget-content awsm-hide">
				<h2>Job Alerts</h2>
				<div class="awsm-jobs-alerts-widget-description">
				<p>Subscribe to get notifications when new job openings are published</p>
				</div>
				<form method="POST" action="https://demo.wpjobopenings.com/" class="awsm-jobs-alerts-form" novalidate="">
				<div class="awsm-jobs-alerts-widget-options">
					<div class="awsm-jobs-alerts-form-group awsm-jobs-alerts-email-group">
						<input type="email" name="awsm_jobs_alerts_email" class="awsm-job-form-control awsm-jobs-alerts-email-field" id="awsm_job_alerts_email" placeholder="Email Address" required="">
					</div>
					<div class="awsm-jobs-alerts-form-group awsm-jobs-alerts-specs-group">
						<div class="awsm-jobs-alerts-specs-group-in">
							<select class="awsm-job-alerts-multiple-select ms-offscreen" multiple="multiple" name="awsm_job_alerts_spec[job-category][]" data-select="All Job Categories" style="">
							<option value="15" selected="">Customer Support</option>
							<option value="10" selected="">Data and Marketing</option>
							<option value="9" selected="">Designs</option>
							<option value="5" selected="">Development</option>
							<option value="7" selected="">Finance</option>
							<option value="12" selected="">Management</option>
							<option value="14" selected="">QA</option>
							</select><div class="ms-parent awsm-job-alerts-multiple-select ms-offscreen" title="" style="width: 1px;"><button type="button" class="ms-choice">
							<span class="">All Job Categories</span>

							<div class="icon-caret"></div>
							</button><div class="ms-drop bottom"><ul>
							<li class="ms-select-all">
							<label>
							<input type="checkbox" data-name="selectAllawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>All Job Categories</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="15" data-key="option_0" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>Customer Support</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="10" data-key="option_1" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>Data and Marketing</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="9" data-key="option_2" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>Designs</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="5" data-key="option_3" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>Development</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="7" data-key="option_4" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>Finance</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="12" data-key="option_5" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>Management</span>
							</label>
							</li>

							<li class=" selected ">
							<label class="">
							<input type="checkbox" value="14" data-key="option_6" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
							<span>QA</span>
							</label>
							</li>
							<li class="ms-no-results">No matches found</li></ul></div></div>
							<div class="ms-parent awsm-job-alerts-multiple-select" title="" style="width: 100%;">
							<button type="button" class="ms-choice">
								<span class="">All Job Categories</span>
								<div class="icon-caret"></div>
							</button>
							<div class="ms-drop bottom">
								<ul>
									<li class="ms-select-all">
										<label>
										<input type="checkbox" data-name="selectAllawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>All Job Categories</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="15" data-key="option_0" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>Customer Support</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="10" data-key="option_1" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>Data and Marketing</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="9" data-key="option_2" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>Designs</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="5" data-key="option_3" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>Development</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="7" data-key="option_4" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>Finance</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="12" data-key="option_5" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>Management</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="14" data-key="option_6" data-name="selectItemawsm_job_alerts_spec[job-category][]" checked="checked">
										<span>QA</span>
										</label>
									</li>
									<li class="ms-no-results">No matches found</li>
								</ul>
							</div>
							</div>
							<input type="hidden" name="awsm_job_original_specs[]" value="job-category">
						</div>
						<div class="awsm-jobs-alerts-specs-group-in">
							<select class="awsm-job-alerts-multiple-select ms-offscreen" multiple="multiple" name="awsm_job_alerts_spec[job-type][]" data-select="All Job Types" style="">
							<option value="4" selected="">Freelance</option>
							<option value="2" selected="">Full Time</option>
							<option value="3" selected="">Part Time</option>
							</select>
							<div class="ms-parent awsm-job-alerts-multiple-select" title="" style="width: 100%;">
							<button type="button" class="ms-choice">
								<span class="">All Job Types</span>
								<div class="icon-caret"></div>
							</button>
							<div class="ms-drop bottom">
								<ul>
									<li class="ms-select-all">
										<label>
										<input type="checkbox" data-name="selectAllawsm_job_alerts_spec[job-type][]" checked="checked">
										<span>All Job Types</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="4" data-key="option_0" data-name="selectItemawsm_job_alerts_spec[job-type][]" checked="checked">
										<span>Freelance</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="2" data-key="option_1" data-name="selectItemawsm_job_alerts_spec[job-type][]" checked="checked">
										<span>Full Time</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="3" data-key="option_2" data-name="selectItemawsm_job_alerts_spec[job-type][]" checked="checked">
										<span>Part Time</span>
										</label>
									</li>
									<li class="ms-no-results">No matches found</li>
								</ul>
							</div>
							</div>
							<input type="hidden" name="awsm_job_original_specs[]" value="job-type">
						</div>
						<div class="awsm-jobs-alerts-specs-group-in">
							<select class="awsm-job-alerts-multiple-select ms-offscreen" multiple="multiple" name="awsm_job_alerts_spec[job-location][]" data-select="All Job Locations" style="">
							<option value="11" selected="">Bangalore</option>
							<option value="8" selected="">France</option>
							<option value="13" selected="">Remote Job</option>
							<option value="6" selected="">San Jose</option>
							</select>
							<div class="ms-parent awsm-job-alerts-multiple-select" title="" style="width: 100%;">
							<button type="button" class="ms-choice">
								<span class="">All Job Locations</span>
								<div class="icon-caret"></div>
							</button>
							<div class="ms-drop bottom">
								<ul>
									<li class="ms-select-all">
										<label>
										<input type="checkbox" data-name="selectAllawsm_job_alerts_spec[job-location][]" checked="checked">
										<span>All Job Locations</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="11" data-key="option_0" data-name="selectItemawsm_job_alerts_spec[job-location][]" checked="checked">
										<span>Bangalore</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="8" data-key="option_1" data-name="selectItemawsm_job_alerts_spec[job-location][]" checked="checked">
										<span>France</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="13" data-key="option_2" data-name="selectItemawsm_job_alerts_spec[job-location][]" checked="checked">
										<span>Remote Job</span>
										</label>
									</li>
									<li class=" selected ">
										<label class="">
										<input type="checkbox" value="6" data-key="option_3" data-name="selectItemawsm_job_alerts_spec[job-location][]" checked="checked">
										<span>San Jose</span>
										</label>
									</li>
									<li class="ms-no-results">No matches found</li>
								</ul>
							</div>
							</div>
							<input type="hidden" name="awsm_job_original_specs[]" value="job-location">
						</div>
					</div>
					<div class="awsm-jobs-alerts-form-group awsm-jobs-alerts-privacy-field-group">
						<label for="awsm_job_alert_privacy_0" class="awsm_job_alert_privacy_label"><input type="checkbox" name="awsm_jobs_alerts_privacy" class="awsm-jobs-alerts-form-req-field" required="" id="awsm_job_alert_privacy_0"> By using this form you agree with the storage and handling of your data by this website.</label>
					</div>
					<div class="awsm-jobs-alerts-form-group awsm-jobs-alerts-button-group">
						<button type="submit" class="awsm-jobs-alerts-btn button button-large">Subscribe</button>
					</div>
				</div>
				<div class="awsm-jobs-alerts-message"></div>
				</form>
			</div>
		</div> -->
		<!-- end -->
	</div>

	<div <?php awsm_block_jobs_view_class( '', $attributes ); ?><?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
		<div class="awsm-job-sort-wrap">
			<div class="awsm-job-results">
				Showing 1 – 10 of 16 results
			</div>
			<div class="awsm-job-sort">
				<label>Sort by</label>
				<select>
					<option>Random</option>
					<option>Date up</option>
					<option>Date down</option>
				</select>
			</div>
		</div>
		<div class="awsm-row awsm-grid-col-3" data-listings="9">
			<?php
				include get_awsm_jobs_template_path( 'block-main', 'block-files' );
			?>
		</div>
		</div>
	</div>
<?php } ?>
	
	<?php
else :
	?>
	<div class="jobs-none-container">
		<p><?php awsm_no_jobs_msg(); ?></p>
	</div>
	<?php
endif;
