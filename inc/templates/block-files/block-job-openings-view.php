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
	<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?> awsm-job-form-plugin-style awsm-job-2-col">
		<div class="awsm-filter-wrap awsm-jobs-alerts-on">
		<form action="https://demo.wpjobopenings.com/wp-admin/admin-ajax.php" method="POST">
			<div class="awsm-filter-item-search">
				<div class="awsm-filter-item-search-in"><label for="awsm-jq-1" class="awsm-sr-only">Search</label><input type="text" id="awsm-jq-1" name="jq" value="" placeholder="Search" class="awsm-job-search awsm-job-form-control"><span class="awsm-job-search-btn awsm-job-search-icon-wrapper"><i class="awsm-job-icon-search"></i></span><span class="awsm-job-search-close-btn awsm-job-search-icon-wrapper awsm-job-hide"><i class="awsm-job-icon-close-circle"></i></span></div>
			</div>
			<a href="https://demo.wpjobopenings.com/#" class="awsm-filter-toggle" role="button" aria-pressed="false">
				<span class="awsm-filter-toggle-text-wrapper awsm-sr-only">Filter by</span>
				<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin">
				<path xmlns="http://www.w3.org/2000/svg" fill="rgb(9.803922%,9.803922%,9.803922%)" d="M 36.417969 19.9375 L 36.417969 17.265625 C 36.417969 16.160156 35.523438 15.265625 34.417969 15.265625 L 21.578125 15.265625 C 20.476562 15.265625 19.578125 16.160156 19.578125 17.265625 L 19.578125 19.9375 L 11 19.9375 L 11 26.9375 L 19.578125 26.9375 L 19.578125 30.105469 C 19.578125 31.210938 20.476562 32.105469 21.578125 32.105469 L 34.417969 32.105469 C 35.523438 32.105469 36.417969 31.210938 36.417969 30.105469 L 36.417969 26.9375 L 89 26.9375 L 89 19.9375 Z M 58.421875 43.578125 C 58.421875 42.476562 57.527344 41.578125 56.421875 41.578125 L 43.582031 41.578125 C 42.480469 41.578125 41.582031 42.476562 41.582031 43.578125 L 41.582031 46.5 L 11 46.5 L 11 53.5 L 41.582031 53.5 L 41.582031 56.421875 C 41.582031 57.527344 42.480469 58.421875 43.582031 58.421875 L 56.421875 58.421875 C 57.527344 58.421875 58.421875 57.527344 58.421875 56.421875 L 58.421875 53.5 L 89 53.5 L 89 46.5 L 58.421875 46.5 Z M 80.417969 70.140625 C 80.417969 69.035156 79.523438 68.140625 78.417969 68.140625 L 65.578125 68.140625 C 64.476562 68.140625 63.578125 69.035156 63.578125 70.140625 L 63.578125 73.0625 L 11 73.0625 L 11 80.0625 L 63.578125 80.0625 L 63.578125 82.984375 C 63.578125 84.085938 64.476562 84.984375 65.578125 84.984375 L 78.417969 84.984375 C 79.523438 84.984375 80.417969 84.085938 80.417969 82.984375 L 80.417969 80.0625 L 89 80.0625 L 89 73.0625 L 80.417969 73.0625 Z M 80.417969 70.140625"></path>
				</svg>
			</a>
			<div class="awsm-jobs-alerts-popup-trigger-btn">
				<a href="https://demo.wpjobopenings.com/#" class="awsm-jobs-alerts-trigger-btn">
				<span>Job Alerts</span>
				<svg width="512" height="512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin">
					<path xmlns="http://www.w3.org/2000/svg" d="M483,351.5v-30c0-23.591-9.086-45.861-25.585-62.708c-12.055-12.309-27.055-20.66-43.415-24.424V206.5h-40v27.868 c-16.36,3.764-31.36,12.115-43.415,24.424C314.086,275.638,305,297.908,305,321.5v33.244c0,4.564-1.258,8.366-3.543,10.707 C285.041,382.266,276,404.039,276,426.757v21.816h116.564C382.207,449.314,374,457.957,374,468.5c0,11.027,8.973,20,20,20 s20-8.973,20-20c0-10.543-8.207-19.186-18.564-19.927H512v-23.375c0-22.057-8.822-43.171-24.842-59.454 C484.516,363.059,483,357.867,483,351.5z M319.81,408.573c2.381-5.498,5.84-10.643,10.27-15.181 C339.701,383.536,345,369.81,345,354.743V321.5c0-26.847,21.616-48.986,48.209-49.41h1.581C421.384,272.514,443,294.653,443,321.5 v30c0,21.818,8.508,35.044,15.645,42.298c4.334,4.405,7.688,9.417,9.951,14.775H319.81z M452,23.5H60c-33.084,0-60,26.916-60,60v256 c0,33.084,26.916,60,60,60h178.99c3.138-14.118,8.704-27.62,16.49-40H60c-11.028,0-20-8.972-20-20V95.763l216,160.663L472,95.763 v122.656c4.91,3.757,9.588,7.888,13.993,12.385c11.049,11.282,19.795,24.248,26.007,38.304V83.5C512,50.416,485.084,23.5,452,23.5z M63.647,63.5h384.706L256,206.574L63.647,63.5z"></path>
				</svg>
				</a>
			</div>
			<div class="awsm-b-filter-items">
				<div class="awsm-b-filter-item" data-filter="job__category_spec">
				<label for="awsm-job-category-filter-option-1">All Job Categories</label>
				<div class="awsm-selectric-wrapper awsm-selectric-awsm-filter-option awsm-selectric-awsm-job-category-filter-option">
					<div class="awsm-selectric-hide-select">
						<div class="awsm-selectric-wrapper awsm-selectric-awsm-filter-option awsm-selectric-awsm-job-category-filter-option"><div class="awsm-selectric-hide-select"><select name="awsm_job_spec[job-category]" class="awsm-filter-option awsm-job-category-filter-option" id="awsm-job-category-filter-option-1" aria-label="All Job Categories" tabindex="-1">
							<option value="">All Job Categories</option>
							<option value="15" data-slug="customer-support">Customer Support</option>
							<option value="10" data-slug="data-and-marketing">Data and Marketing</option>
							<option value="9" data-slug="designs">Designs</option>
							<option value="5" data-slug="development">Development</option>
							<option value="7" data-slug="finance">Finance</option>
							<option value="12" data-slug="management">Management</option>
							<option value="14" data-slug="qa">QA</option>
						</select></div><div class="awsm-selectric"><span class="label">All Job Categories</span><span class="awsm-selectric-arrow-drop">▾</span></div><div class="awsm-selectric-items" tabindex="-1"><div class="awsm-selectric-scroll"><ul><li data-index="0" class="selected">All Job Categories</li><li data-index="1" class="">Customer Support</li><li data-index="2" class="">Data and Marketing</li><li data-index="3" class="">Designs</li><li data-index="4" class="">Development</li><li data-index="5" class="">Finance</li><li data-index="6" class="">Management</li><li data-index="7" class="last">QA</li></ul></div></div><input class="awsm-selectric-input" tabindex="-1"></div>
					</div>
					<div class="awsm-selectric"><span class="label">All Job Categories</span><span class="awsm-selectric-arrow-drop">▾</span></div>
					<div class="awsm-selectric-items" tabindex="-1">
						<div class="awsm-selectric-scroll">
							<ul>
							<li data-index="0" class="selected">All Job Categories</li>
							<li data-index="1" class="">Customer Support</li>
							<li data-index="2" class="">Data and Marketing</li>
							<li data-index="3" class="">Designs</li>
							<li data-index="4" class="">Development</li>
							<li data-index="5" class="">Finance</li>
							<li data-index="6" class="">Management</li>
							<li data-index="7" class="last">QA</li>
							</ul>
						</div>
					</div>
					<input class="awsm-selectric-input" tabindex="0">
				</div>
				</div>
				<div class="awsm-b-filter-item" data-filter="job__type_spec">
				<label for="awsm-job-type-filter-option-1">All Job Types</label>
				<div class="awsm-selectric-wrapper awsm-selectric-awsm-filter-option awsm-selectric-awsm-job-type-filter-option">
					<div class="awsm-selectric-hide-select">
						<div class="awsm-selectric-wrapper awsm-selectric-awsm-filter-option awsm-selectric-awsm-job-type-filter-option"><div class="awsm-selectric-hide-select"><select name="awsm_job_spec[job-type]" class="awsm-filter-option awsm-job-type-filter-option" id="awsm-job-type-filter-option-1" aria-label="All Job Types" tabindex="-1">
							<option value="">All Job Types</option>
							<option value="4" data-slug="freelance">Freelance</option>
							<option value="2" data-slug="full-time">Full Time</option>
							<option value="3" data-slug="part-time">Part Time</option>
						</select></div><div class="awsm-selectric"><span class="label">All Job Types</span><span class="awsm-selectric-arrow-drop">▾</span></div><div class="awsm-selectric-items" tabindex="-1"><div class="awsm-selectric-scroll"><ul><li data-index="0" class="selected">All Job Types</li><li data-index="1" class="">Freelance</li><li data-index="2" class="">Full Time</li><li data-index="3" class="last">Part Time</li></ul></div></div><input class="awsm-selectric-input" tabindex="-1"></div>
					</div>
					<div class="awsm-selectric"><span class="label">All Job Types</span><span class="awsm-selectric-arrow-drop">▾</span></div>
					<div class="awsm-selectric-items" tabindex="-1">
						<div class="awsm-selectric-scroll">
							<ul>
							<li data-index="0" class="selected">All Job Types</li>
							<li data-index="1" class="">Freelance</li>
							<li data-index="2" class="">Full Time</li>
							<li data-index="3" class="last">Part Time</li>
							</ul>
						</div>
					</div>
					<input class="awsm-selectric-input" tabindex="0">
				</div>
				</div>
				<div class="awsm-b-filter-item" data-filter="job__location_spec">
				<label for="awsm-job-location-filter-option-1">All Job Locations</label>
				<div class="awsm-selectric-wrapper awsm-selectric-awsm-filter-option awsm-selectric-awsm-job-location-filter-option">
					<div class="awsm-selectric-hide-select">
						<div class="awsm-selectric-wrapper awsm-selectric-awsm-filter-option awsm-selectric-awsm-job-location-filter-option"><div class="awsm-selectric-hide-select"><select name="awsm_job_spec[job-location]" class="awsm-filter-option awsm-job-location-filter-option" id="awsm-job-location-filter-option-1" aria-label="All Job Locations" tabindex="-1">
							<option value="">All Job Locations</option>
							<option value="11" data-slug="bangalore">Bangalore</option>
							<option value="8" data-slug="france">France</option>
							<option value="13" data-slug="remote-job">Remote Job</option>
							<option value="6" data-slug="san-jose">San Jose</option>
						</select></div><div class="awsm-selectric"><span class="label">All Job Locations</span><span class="awsm-selectric-arrow-drop">▾</span></div><div class="awsm-selectric-items" tabindex="-1"><div class="awsm-selectric-scroll"><ul><li data-index="0" class="selected">All Job Locations</li><li data-index="1" class="">Bangalore</li><li data-index="2" class="">France</li><li data-index="3" class="">Remote Job</li><li data-index="4" class="last">San Jose</li></ul></div></div><input class="awsm-selectric-input" tabindex="-1"></div>
					</div>
					<div class="awsm-selectric"><span class="label">All Job Locations</span><span class="awsm-selectric-arrow-drop">▾</span></div>
					<div class="awsm-selectric-items" tabindex="-1">
						<div class="awsm-selectric-scroll">
							<ul>
							<li data-index="0" class="selected">All Job Locations</li>
							<li data-index="1" class="">Bangalore</li>
							<li data-index="2" class="">France</li>
							<li data-index="3" class="">Remote Job</li>
							<li data-index="4" class="last">San Jose</li>
							</ul>
						</div>
					</div>
					<input class="awsm-selectric-input" tabindex="0">
				</div>
				</div>
				<div class="awsm-b-filter-item" data-filter="job__location_spec">
				<div class="awsm-filter-list">
						<label for="awsm-job-location-filter-option-1">All Job Locations</label>
					<div class="awm-filter-list-items">
					<div class="awsm-filter-list-item">
						<label>
							<input type="checkbox">
							<div>
							<span class="awsm-filter-checkbox-item">
								<svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M8.45447 0.848088L3.99989 5.30315L1.66632 2.96958L1.52489 2.82816L1.38347 2.96958L0.676473 3.67658L0.535051 3.818L0.676473 3.95942L3.85847 7.14142L3.99989 7.28284L4.14132 7.14142L9.44482 1.83792L9.58629 1.69645L9.44477 1.55503L8.73727 0.848031L8.59584 0.706702L8.45447 0.848088Z" fill="white" stroke="white" stroke-width="0.4"></path></svg>
							</span>
							Bangalore
							<span class="awsm-filter-item-count">10</span>
							</div>
						</label>
					</div>
					<div class="awsm-filter-list-item">
						<label>
							<input type="checkbox">
							<div>
							<span class="awsm-filter-checkbox-item">
								<svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M8.45447 0.848088L3.99989 5.30315L1.66632 2.96958L1.52489 2.82816L1.38347 2.96958L0.676473 3.67658L0.535051 3.818L0.676473 3.95942L3.85847 7.14142L3.99989 7.28284L4.14132 7.14142L9.44482 1.83792L9.58629 1.69645L9.44477 1.55503L8.73727 0.848031L8.59584 0.706702L8.45447 0.848088Z" fill="white" stroke="white" stroke-width="0.4"></path></svg>
							</span>
							France
							<span class="awsm-filter-item-count">10</span>
							</div>
						</label>
					</div>
					<div class="awsm-filter-list-item">
						<label>
							<input type="checkbox">
							<div>
							<span class="awsm-filter-checkbox-item">
								<svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M8.45447 0.848088L3.99989 5.30315L1.66632 2.96958L1.52489 2.82816L1.38347 2.96958L0.676473 3.67658L0.535051 3.818L0.676473 3.95942L3.85847 7.14142L3.99989 7.28284L4.14132 7.14142L9.44482 1.83792L9.58629 1.69645L9.44477 1.55503L8.73727 0.848031L8.59584 0.706702L8.45447 0.848088Z" fill="white" stroke="white" stroke-width="0.4"></path></svg>
							</span>
							Remote Job
							<span class="awsm-filter-item-count">10</span>
							</div>
						</label>
					</div>
					<div class="awsm-filter-list-item">
						<label>
							<input type="checkbox">
							<div>
							<span class="awsm-filter-checkbox-item">
								<svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M8.45447 0.848088L3.99989 5.30315L1.66632 2.96958L1.52489 2.82816L1.38347 2.96958L0.676473 3.67658L0.535051 3.818L0.676473 3.95942L3.85847 7.14142L3.99989 7.28284L4.14132 7.14142L9.44482 1.83792L9.58629 1.69645L9.44477 1.55503L8.73727 0.848031L8.59584 0.706702L8.45447 0.848088Z" fill="white" stroke="white" stroke-width="0.4"></path></svg>
							</span>
							San Jose
							<span class="awsm-filter-item-count">10</span>
							</div>
						</label>
					</div>
					</div>
				</div>
				</div>
			</div>
			<input type="hidden" name="action" value="jobfilter">
		</form>
		<div class="awsm-jobs-alerts-widget-wrapper">
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
		</div>
		</div>
		<div class="awsm-job-listings">
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
	<?php
else :
	?>
	<div class="jobs-none-container">
		<p><?php awsm_no_jobs_msg(); ?></p>
	</div>
	<?php
endif;
