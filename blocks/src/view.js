'use strict';

jQuery(function($) { 
	var rootWrapperSelector = '.awsm-b-job-wrap';
	var wrapperSelector = '.awsm-b-job-listings';

	/* ========== Job Search and Filtering ========== */

	var filterSelector = '.awsm-b-filter-wrap';
	var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	var triggerFilter = true;

	function getListingsData($wrapper) { 
		var data = [];
		var parsedListingsAttrs = [ 'listings', 'specs', 'search', 'lang', 'taxonomy', 'termId' ];

		/* added for block */
		parsedListingsAttrs.push('awsm-layout');
		parsedListingsAttrs.push('awsm-hide-expired-jobs');
		parsedListingsAttrs.push('awsm-other-options');
		/* end */
		$(document).trigger('awsmJobBlockListingsData', [ parsedListingsAttrs ]);

		var dataAttrs = $wrapper.data();
		$.each(dataAttrs, function(dataAttr, value) { 
			if ($.inArray(dataAttr, parsedListingsAttrs) === -1) {
				data.push({
					name: dataAttr,
					value: value
				});
			}
		});
		return data;
	}

	function awsmJobFilters($rootWrapper) { 
		var $wrapper = $rootWrapper.find(wrapperSelector);
		var $filterForm = $rootWrapper.find(filterSelector + ' form');
		var formData = $filterForm.serializeArray();
		var listings = $wrapper.data('listings');
		var specs = $wrapper.data('specs');
		

		/* added for block */
		var layout 				= $wrapper.data('awsm-layout');
		var hide_expired_jobs   = $wrapper.data('awsm-hide-expired-jobs'); 
		var other_options 		= $wrapper.data('awsm-other-options'); 
		/* end */
		formData.push({
			name: 'listings_per_page',
			value: listings
		});
		if (typeof specs !== 'undefined') {
			formData.push({
				name: 'shortcode_specs',
				value: specs
			});
		}

		/* added for block */
		if (typeof layout !== 'undefined') {
			formData.push({
				name: 'awsm-layout',
				value: layout
			});
		}

		if (typeof hide_expired_jobs !== 'undefined') {
			formData.push({
				name: 'awsm-hide-expired-jobs',
				value: hide_expired_jobs
			});
		}

		if (typeof other_options !== 'undefined') {
			formData.push({
				name: 'awsm-other-options',
				value: other_options
			});
		}
		/* end */

		var listingsData = getListingsData($wrapper);
		if (listingsData.length > 0) {
			formData = formData.concat(listingsData);
		}

		// Trigger custom event to provide formData
		$(document).trigger('awsmJobBlockFiltersFormData', [$wrapper,formData]);

		if (triggerFilter) {

			// stop the duplicate requests.
			triggerFilter = false;

			// now, make the request.
			$.ajax({
				url: $filterForm.attr('action'),
				beforeSend: function() {
					$wrapper.addClass('awsm-b-jobs-loading');
				},
				data: formData,
				type: $filterForm.attr('method')
			}).done(function(data) {
				$wrapper.html(data);
				var $searchControl = $rootWrapper.find('.awsm-b-job-search');
				if ($searchControl.length > 0) {
					if ($searchControl.val().length > 0) {
						$rootWrapper.find('.awsm-b-job-search-btn').addClass('awsm-b-job-hide');
						$rootWrapper.find('.awsm-b-job-search-close-btn').removeClass('awsm-b-job-hide');
					} else {
						$rootWrapper.find('.awsm-b-job-search-btn').removeClass('awsm-b-job-hide');
					}
				}
				$(document).trigger('awsmjobs_filtered_listings', [ $rootWrapper, data ]);
			}).fail(function(xhr) {
				// eslint-disable-next-line no-console
				console.log(xhr);
			}).always(function() {
				$wrapper.removeClass('awsm-b-jobs-loading');
				triggerFilter = true;
			});
		}
	}

	function filterCheck($filterForm) {
		var check = false;
		if ($filterForm.length > 0) {
			var $filterOption = $filterForm.find('.awsm-b-filter-option');
			$filterOption.each(function() {
				if ($(this).val().length > 0) {
					check = true;
				}
			});
		}
		return check;
	}

	function searchJobs($elem) {
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var searchQuery = $rootWrapper.find('.awsm-b-job-search').val();
		$rootWrapper.find(wrapperSelector).data('search', searchQuery);
		if (searchQuery.length === 0) {
			$rootWrapper.find('.awsm-b-job-search-icon-wrapper').addClass('awsm-b-job-hide');
		}
		setPaginationBase($rootWrapper, 'jq', searchQuery);
		if (awsmJobsPublic.deep_linking.search) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery('jq', searchQuery, $paginationBase.val());
		}
		awsmJobFilters($rootWrapper);
	}

	if ($(rootWrapperSelector).length > 0) {
		$(rootWrapperSelector).each(function() {
			var $currentWrapper = $(this);
			var $filterForm = $currentWrapper.find(filterSelector + ' form');
			if (awsmJobsPublic.is_search.length > 0 || filterCheck($filterForm)) {
				triggerFilter = true;
				awsmJobFilters($currentWrapper);
			}
		});
	}

	var updateQuery = function(key, value, url) {
		url = typeof url !== 'undefined' ? url : currentUrl;
		url = url.split('?')[0];
		var searchParams = new URLSearchParams(document.location.search);
		if (searchParams.has('paged')) {
			searchParams.delete('paged');
		}
		if (value.length > 0) {
			searchParams.set(key, value);
		} else {
			searchParams.delete(key);
		}
		var modQueryString = searchParams.toString();
		if (modQueryString.length > 0) {
			modQueryString = '?' + modQueryString;
		}
		window.history.replaceState({}, '', url + modQueryString);
	};

	var setPaginationBase = function($rootWrapper, key, value) {
		var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
		if ($paginationBase.length > 0) {
			var splittedURL = $paginationBase.val().split('?');
			var queryString = '';
			if (splittedURL.length > 1) {
				queryString = splittedURL[1];
			}
			var searchParams = new URLSearchParams(queryString);
			if (value.length > 0) {
				searchParams.set(key, value);
			} else {
				searchParams.delete(key);
			}
			$paginationBase.val(splittedURL[0] + '?' + searchParams.toString());
			$rootWrapper.find('input[name="paged"]').val(1);
		}
	};

	$(filterSelector + ' .awsm-b-filter-option').on('change', function(e) { 
		e.preventDefault();
		var $elem = $(this);
		var $selected = $elem.find('option:selected');
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var currentSpec = $elem.parents('.awsm-b-filter-item').data('filter');
		var slug = $selected.data('slug');
		slug = typeof slug !== 'undefined' ? slug : '';
		setPaginationBase($rootWrapper, currentSpec, slug);
		if (awsmJobsPublic.deep_linking.spec) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery(currentSpec, slug, $paginationBase.val());
		}
		awsmJobFilters($rootWrapper);
	});

	$(filterSelector + ' .awsm-b-job-search-btn').on('click', function() {
		searchJobs($(this));
	});

	$(filterSelector + ' .awsm-b-job-search-close-btn').on('click', function() {
		var $elem = $(this);
		$elem.parents(rootWrapperSelector).find('.awsm-b-job-search').val('');
		searchJobs($elem);
	});

	$(filterSelector + ' .awsm-b-job-search').on('keypress', function(e) {
		if (e.which == 13) {
			e.preventDefault();
			searchJobs($(this));
		}
	});

	/* ========== Job Listings Load More ========== */
	$(wrapperSelector).on('click', '.awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers', function(e) {
		e.preventDefault(); 
		var $triggerElem = $(this);
		var isDefaultPagination = $triggerElem.hasClass('awsm-b-load-more-btn');
		var paged = 1;
		var wpData = [];
		var $mainContainer = $triggerElem.parents(rootWrapperSelector);
		var $listingsContainer = $mainContainer.find(wrapperSelector);
		var $paginationWrapper = $triggerElem.parents('.awsm-b-jobs-pagination');
		var listings = $listingsContainer.data('listings');
		var specs = $listingsContainer.data('specs');
		var lang = $listingsContainer.data('lang');
		var searchQuery = $listingsContainer.data('search');

		/* added for block */
		var layout = $listingsContainer.data('awsm-layout');
		var hide_expired_jobs = $listingsContainer.data('awsm-hide-expired-jobs');
		var other_options = $listingsContainer.data('awsm-other-options');
		/* end */

		if (isDefaultPagination) {
			$triggerElem.prop('disabled', true);
			paged = $triggerElem.data('page');
			paged = (typeof paged == 'undefined') ? 1 : paged;
		} else {
			$triggerElem.parents('.page-numbers').find('.page-numbers').removeClass('current').removeAttr('aria-current');
			$triggerElem.addClass('current').attr('aria-current', 'page');
		}
		$paginationWrapper.addClass('awsm-b-jobs-pagination-loading');

		// filters
		var $filterForm = $mainContainer.find(filterSelector + ' form');
		if (filterCheck($filterForm)) {
			var $filterOption = $filterForm.find('.awsm-b-filter-option');
			wpData = $filterOption.serializeArray();
		}

		if (! isDefaultPagination) {
			var paginationBaseURL = $triggerElem.attr('href');
			var splittedURL = paginationBaseURL.split('?');
			var queryString = '';
			if (splittedURL.length > 1) {
				var searchParams = new URLSearchParams(splittedURL[1]);
				paged = searchParams.get('paged');
				searchParams.delete('paged');
				if (searchParams.toString().length > 0) {
					queryString = '?' + searchParams.toString();
				}
			}
			paginationBaseURL = splittedURL[0] + queryString;
			wpData.push({
				name: 'awsm_pagination_base',
				value: splittedURL[0] + queryString
			});
			if (awsmJobsPublic.deep_linking.pagination) {
				updateQuery('paged', paged, paginationBaseURL);
			}
		}

		// taxonomy archives
		if (awsmJobsPublic.is_tax_archive) {
			var taxonomy = $listingsContainer.data('taxonomy');
			var termId = $listingsContainer.data('termId');
			if (typeof taxonomy !== 'undefined' && typeof termId !== 'undefined') {
				wpData.push({
					name: 'awsm_job_spec[' + taxonomy + ']',
					value: termId
				});
			}
		}

		wpData.push({
			name: 'action',
			value: 'block_loadmore'
		}, {
			name: 'paged',
			value: paged
		});
		if (typeof listings !== 'undefined') {
			wpData.push({
				name: 'listings_per_page',
				value: listings
			});
		}
		if (typeof specs !== 'undefined') {
			wpData.push({
				name: 'shortcode_specs',
				value: specs
			});
		}

		/* added for block */
		if (typeof layout !== 'undefined') {
			wpData.push({
				name: 'awsm-layout',
				value: layout
			});
		}
		if (typeof hide_expired_jobs !== 'undefined') {
			wpData.push({
				name: 'awsm-hide-expired-jobs',
				value: hide_expired_jobs
			});
		}
		if (typeof other_options !== 'undefined') {
			wpData.push({
				name: 'awsm-other-options',
				value: other_options
			});
		}
		
		if (typeof lang !== 'undefined') {
			wpData.push({
				name: 'lang',
				value: lang
			});
		}
		if (typeof searchQuery !== 'undefined') {
			wpData.push({
				name: 'jq',
				value: searchQuery
			});
		}

		$(document).trigger('awsmjobs_block_load_more', [ $listingsContainer,wpData ]);
		var listingsData = getListingsData($listingsContainer);
		if (listingsData.length > 0) {
			wpData = wpData.concat(listingsData);
		}

		// now, handle ajax
		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			data: $.param(wpData),
			type: 'POST',
			beforeSend: function() {
				if (isDefaultPagination) {
					$triggerElem.text(awsmJobsPublic.i18n.loading_text);
				} else {
					$listingsContainer.addClass('awsm-b-jobs-loading');
				}
			}
		}).done(function(data) {
			if (data) {
				var effectDuration = $paginationWrapper.data('effectDuration');
				$paginationWrapper.remove();
				if (isDefaultPagination) {
					$listingsContainer.append(data);
				} else {
					$listingsContainer.html(data);
					$listingsContainer.removeClass('awsm-b-jobs-loading');
					if (typeof effectDuration !== 'undefined') {
						effectDuration = isNaN(effectDuration) ? effectDuration : Number(effectDuration);
						$('html, body').animate({
							scrollTop: $mainContainer.offset().top - 25
						}, effectDuration);
					}
				}
			} else {
				$triggerElem.remove();
			}
			$(document).trigger('awsmjobs_load_more', [ $triggerElem, data ]);
		}).fail(function(xhr) {
			// eslint-disable-next-line no-console
			console.log(xhr);
		});
	});

	/**
	 * Handle the filters toggle button in the job listing.
	 */
	$(document).on('click', '.awsm-b-filter-toggle', function(e) {
		e.preventDefault();
		var $elem = $(this);
		$elem.toggleClass('awsm-on');
		if ($elem.hasClass('awsm-on')) {
			$elem.attr('aria-pressed', 'true');
		} else {
			$elem.attr('aria-pressed', 'false');
		}
		var $parent = $elem.parent();
		$parent.find('.awsm-b-filter-items').slideToggle();
	});

	/**
	 * Handle the responsive styles for filters in the job listing when search is enabled.
	 */
	function filtersResponsiveStylesHandler() {
		var $filtersWrap = $('.awsm-b-filter-wrap').not('.awsm-b-no-search-filter-wrap');
		$filtersWrap.each(function() {
			var $wrapper = $(this); 
			var filterFirstTop = $wrapper.find('.awsm-b-filter-item').first().offset().top;
			var filterLastTop = $wrapper.find('.awsm-b-filter-item').last().offset().top;
			if(window.innerWidth < 768) {
				$wrapper.removeClass('awsm-b-full-width-search-filter-wrap');
				return;
			}
			if (filterLastTop > filterFirstTop) {
				$wrapper.addClass('awsm-b-full-width-search-filter-wrap');
			}
		});
	}
	if ($('.awsm-b-filter-wrap').not('.awsm-b-no-search-filter-wrap').length > 0) {
		filtersResponsiveStylesHandler();
		$(window).on('resize', filtersResponsiveStylesHandler);
	}
});

