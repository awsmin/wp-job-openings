/* global awsmJobsPublic */

'use strict';

jQuery(function($) {  
	var rootWrapperSelector = '.awsm-job-wrap';
	var wrapperSelector = '.awsm-job-listings';

	/* ========== Job Search and Filtering ========== */

	var filterSelector = '.awsm-filter-wrap';
	var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	var triggerFilter = true;

	function getListingsData($wrapper) {  
		var data = [];
		var parsedListingsAttrs = [ 'listings', 'specs', 'search', 'lang', 'taxonomy', 'termId' ];
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

		$rootWrapper.find('.awsm-filter-item').each(function() {
			var currentLoopSpec = $(this).data('filter');
			var searchParams = new URLSearchParams(document.location.search);
			var currentSpecQueryVal = searchParams.get(currentLoopSpec); 
			var $currentOption = $(this).find('.awsm-filter-option');
			if ($currentOption.val().length === 0 && currentSpecQueryVal && currentSpecQueryVal.length > 0) {
				formData.forEach(function(item) {
					if (item.name === $currentOption.attr('name')) {
						item.value = '-1';
					}
				});
			}
		});
		
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

		var listingsData = getListingsData($wrapper);
		if (listingsData.length > 0) {
			formData = formData.concat(listingsData);
		}
		if (triggerFilter) {

			// stop the duplicate requests.
			triggerFilter = false;

			// now, make the request.
			$.ajax({
				url: $filterForm.attr('action'),
				beforeSend: function() { 
					$wrapper.addClass('awsm-jobs-loading');
				},
				data: formData,
				type: $filterForm.attr('method')
			}).done(function(data) {
				$wrapper.html(data);
				var $searchControl = $rootWrapper.find('.awsm-job-search');
				if ($searchControl.length > 0) {
					if ($searchControl.val().length > 0) {
						$rootWrapper.find('.awsm-job-search-btn').addClass('awsm-job-hide');
						$rootWrapper.find('.awsm-job-search-close-btn').removeClass('awsm-job-hide');
					} else {
						$rootWrapper.find('.awsm-job-search-btn').removeClass('awsm-job-hide');
					}
				}
				$(document).trigger('awsmjobs_filtered_listings', [ $rootWrapper, data ]);
			}).fail(function(xhr) {
				// eslint-disable-next-line no-console
				console.log(xhr);
			}).always(function() {
				$wrapper.removeClass('awsm-jobs-loading');
				triggerFilter = true;
			});
		}
	}

	function filterCheck($filterForm) {
		var check = false;
		if ($filterForm.length > 0) {
			var $filterOption = $filterForm.find('.awsm-filter-option');
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
		var searchQuery = $rootWrapper.find('.awsm-job-search').val();
		$rootWrapper.find(wrapperSelector).data('search', searchQuery);
		if (searchQuery.length === 0) {
			$rootWrapper.find('.awsm-job-search-icon-wrapper').addClass('awsm-job-hide');
		}
		setPaginationBase($rootWrapper, 'jq', searchQuery);
		if (awsmJobsPublic.deep_linking.search) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery('jq', searchQuery, $paginationBase.val());
		}
		awsmJobFilters($rootWrapper);
	}

	// Updated updateQuery function
	var updateQuery = function(key, value, url) {
		url = typeof url !== 'undefined' ? url : currentUrl;
		url = url.split('?')[0];
		var searchParams = new URLSearchParams(document.location.search);
		if (searchParams.has('paged')) {
			searchParams.delete('paged');
		}
		if (searchParams.has('page')) {
			searchParams.delete('page');
		}
		value = value !== undefined && value !== null ? String(value) : '';

		if (value !== '') {
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

	if ($('.awsm-job-no-more-jobs-get').length > 0) {
		$('.awsm-job-listings').hide();
		$('.awsm-job-no-more-jobs-get').slice(1).hide();
	}

	$(filterSelector + ' .awsm-filter-option').on('change', function(e) { 
		e.preventDefault();
		$('.awsm-job-listings').show();
		var $elem = $(this);
		var $selected = $elem.find('option:selected');
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var currentSpec = $elem.parents('.awsm-filter-item').data('filter');
		var slug = $selected.data('slug');
		slug = typeof slug !== 'undefined' ? slug : '';
		if ($('.awsm-job-listings').length > 0) {
			$rootWrapper.find('.awsm-job-no-more-jobs-get').hide();
		}
		setPaginationBase($rootWrapper, currentSpec, slug);
		if (awsmJobsPublic.deep_linking.spec) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery(currentSpec, slug, $paginationBase.val());
		}
		awsmJobFilters($rootWrapper);
	});

	$(filterSelector + ' .awsm-job-search-btn').on('click', function() {
		searchJobs($(this));
	});

	$(filterSelector + ' .awsm-job-search-close-btn').on('click', function() {
		var $elem = $(this);
		$elem.parents(rootWrapperSelector).find('.awsm-job-search').val('');
		searchJobs($elem);
	});

	$(filterSelector + ' .awsm-job-search').on('keypress', function(e) {
		if (e.which == 13) {
			e.preventDefault();
			searchJobs($(this));
		}
	});

	/* ========== Job Listings Load More ========== */

	$(wrapperSelector).on('click', '.awsm-jobs-pagination .awsm-load-more-btn, .awsm-jobs-pagination a.page-numbers', function(e) {
		e.preventDefault(); 
		var $triggerElem = $(this);
		var isDefaultPagination = $triggerElem.hasClass('awsm-load-more-btn');
		var paged = 1;
		var wpData = [];
		var $mainContainer = $triggerElem.parents(rootWrapperSelector);
		var $listingsContainer = $mainContainer.find(wrapperSelector);
		var $paginationWrapper = $triggerElem.parents('.awsm-jobs-pagination');
		var listings = $listingsContainer.data('listings');
		var specs = $listingsContainer.data('specs');
		var lang = $listingsContainer.data('lang');
		var searchQuery = $listingsContainer.data('search');

		if (isDefaultPagination) {
			$triggerElem.prop('disabled', true);
			paged = $triggerElem.data('page');
			paged = (typeof paged == 'undefined') ? 1 : paged;
		} else {
			// This visual update is optional since HTML gets replaced, but good for immediate feedback
			// Just remove it if it causes issues - the server should handle the current state
			$paginationWrapper.find('.page-numbers').removeClass('current').removeAttr('aria-current');
			$triggerElem.addClass('current').attr('aria-current', 'page');
		}
		$paginationWrapper.addClass('awsm-jobs-pagination-loading');

		// filters
		var $filterForm = $mainContainer.find(filterSelector + ' form');
		if (filterCheck($filterForm)) {
			var $filterOption = $filterForm.find('.awsm-filter-option');
			wpData = $filterOption.serializeArray();
		}

		if (! isDefaultPagination) {
			var paginationBaseURL = $triggerElem.attr('href');
			var splittedURL = paginationBaseURL.split('?');
			var queryString = '';
			var isHomepage = window.awsmJobsPublic && awsmJobsPublic.is_homepage;
			var pageKey = isHomepage ? 'page' : 'paged';
			
			if (splittedURL.length > 1) {
				var searchParams = new URLSearchParams(splittedURL[1]);
				paged = searchParams.get(pageKey) || searchParams.get(pageKey === 'page' ? 'paged' : 'page');
				
				if (!paged) {
					paged = 1;
				}
				
				searchParams.delete('page');
				searchParams.delete('paged');
				
				if (searchParams.toString().length > 0) {
					queryString = '?' + searchParams.toString();
				}
			} else {
				var pageMatch = paginationBaseURL.match(/\/page\/(\d+)\/?/);
				if (pageMatch) {
					paged = pageMatch[1];
				} else {
					paged = 1;
				}
			}
			
			paginationBaseURL = splittedURL[0] + queryString;
			wpData.push({
				name: 'awsm_pagination_base',
				value: splittedURL[0] + queryString
			});
			
			if (awsmJobsPublic.deep_linking.pagination) {
				updateQuery(pageKey, paged, paginationBaseURL);
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
			value: 'loadmore'
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
					$listingsContainer.addClass('awsm-jobs-loading');
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
					$listingsContainer.removeClass('awsm-jobs-loading');
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

	/* ========== Custom select box - selectric ========== */

	function awsmDropDown($elem) {
		if ('selectric' in awsmJobsPublic.vendors && awsmJobsPublic.vendors.selectric) {
			$elem.selectric({
				onInit: function(select, selectric) {
					var id = select.id; 
					if (selectric && selectric.elements && selectric.elements.input) {
						var $input = $(selectric.elements.input);
						$(select).attr('id', 'selectric-' + id);
						$input.attr('id', id);
					}
				},
				arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
				customClass: {
					prefix: 'awsm-selectric',
					camelCase: false
				}
			});
		}
	}
	awsmDropDown($('.awsm-job-select-control'));
	awsmDropDown($('.awsm-filter-item select'));

	/**
	 * Handle the filters toggle button in the job listing.
	 */
	$(document).on('click', '.awsm-filter-toggle', function(e) {
		e.preventDefault();
		var $elem = $(this);
		$elem.toggleClass('awsm-on');
		if ($elem.hasClass('awsm-on')) {
			$elem.attr('aria-pressed', 'true');
		} else {
			$elem.attr('aria-pressed', 'false');
		}
		var $parent = $elem.parent();
		$parent.find('.awsm-filter-items').slideToggle();
	});

	/**
	 * Handle the responsive styles for filters in the job listing when search is enabled.
	 */
	function filtersResponsiveStylesHandler() {
		var $filtersWrap = $('.awsm-filter-wrap').not('.awsm-no-search-filter-wrap');
		$filtersWrap.each(function() {
			var $wrapper = $(this);
			var filterFirstTop = $wrapper.find('.awsm-filter-item').first().offset().top;
			var filterLastTop = $wrapper.find('.awsm-filter-item').last().offset().top;
			if (filterLastTop > filterFirstTop) {
				$wrapper.addClass('awsm-full-width-search-filter-wrap');
			} else {
				$wrapper.removeClass('awsm-full-width-search-filter-wrap');
			}
		});
	}
	if ($('.awsm-filter-wrap').not('.awsm-no-search-filter-wrap').length > 0) {
		filtersResponsiveStylesHandler();
		$(window).on('resize', filtersResponsiveStylesHandler);
	}
});
