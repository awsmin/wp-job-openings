/* global awsmJobsPublic */

'use strict';

jQuery(function($) {  
	var rootWrapperSelector = '.awsm-job-wrap';
	var wrapperSelector 	= '.awsm-job-listings';
	var sectionSelector 	= '.awsm-job-listing-items'; 

	/* ========== Job Search and Filtering ========== */

	var filterSelector = '.awsm-filter-wrap';
	var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	var triggerFilter = true;

	function getListingsData($wrapper) {  
		var data = [];
		var parsedListingsAttrs = [ 'listings', 'specs', 'search', 'lang', 'taxonomy', 'termId' ];

		parsedListingsAttrs.push('awsm-selected-terms');

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
		var $rowWrapper = $wrapper.find(sectionSelector);
		var $filterForm = $rootWrapper.find(filterSelector + ' form'); 
		var formData = [];
	
		if ($filterForm.length > 0) {
			// Form exists → Serialize form data
			formData = $filterForm.serializeArray();
			var formMethod = $filterForm.attr('method') ? $filterForm.attr('method').toUpperCase() : 'POST';
		} else {
			// Form is missing → Manually construct data
			formData.push({ name: 'action', value: 'jobfilter' }); // Ensure action is included
			var formMethod = 'POST';
		}
	
		// Get additional data (if available)
		var listings = $wrapper.data('listings');
		var specs = $wrapper.data('specs');
		var selected_terms = $wrapper.data('awsm-selected-terms');
	
		if (listings) {
			formData.push({ name: 'listings_per_page', value: listings });
		}
		if (specs) {
			formData.push({ name: 'shortcode_specs', value: specs });
		}
		if (selected_terms) {
			formData.push({ name: 'awsm-selected-terms', value: JSON.stringify(selected_terms) });
		}
	
		// Perform AJAX call only if triggerFilter is true
		if (triggerFilter) {
			triggerFilter = false;
			$.ajax({
				url: $filterForm.length > 0 ? $filterForm.attr('action') : awsmJobsPublic.ajaxurl, // Use AJAX URL if form is missing
				type: formMethod,
				data: formData,
				beforeSend: function() {
					$wrapper.addClass('awsm-jobs-loading');
				}
			}).done(function(data) {
				$rowWrapper.html(data);
				$(document).trigger('awsmjobs_filtered_listings', [$rootWrapper, data]);
			}).fail(function(xhr) {
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

	if ($('.awsm-job-no-more-jobs-get').length > 0) {
		$('.awsm-job-listings').hide();
		$('.awsm-job-no-more-jobs-get').slice(1).hide();
	}

	$(filterSelector + ' .awsm-filter-option').on('change', function (e) {
		e.preventDefault();
		$('.awsm-job-listings').show();
	
		var $elem = $(this); // The original <select> element
		var $rootWrapper = $elem.closest(rootWrapperSelector);
		var currentSpec = $elem.closest('.awsm-filter-item').data('filter');
	
		var isMultiple = $elem.prop('multiple'); // Check if it's a multiple select
		var allOptions = $elem.find('option');
		var firstOption = allOptions.eq(0); // "All Job Type"
		var selectedOptions = $elem.find('option:selected');
		var isAllSelected = firstOption.prop('selected');
	
		var allLiItems = $rootWrapper.find('ul li');
		var firstLiItem = allLiItems.eq(0); // "All Job Type" in <ul>
		var selectedLiItems = allLiItems.filter('.selected');
	
		var slugs = [];
	
		if (isMultiple) {
			if (isAllSelected) {
				// **"All" is selected → Select all**
				allOptions.prop('selected', true).addClass('selected');
				allLiItems.addClass('selected');
	
				slugs = allOptions.slice(1).map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);
			} else if (selectedOptions.length === 0) {
				// **Nothing is selected → Deselect everything**
				allOptions.prop('selected', false).removeClass('selected');
				allLiItems.removeClass('selected');
				slugs = [];
			} else { 
				// **Handle individual selection**
				//allOptions.prop('selected', false).removeClass('selected');
				//allLiItems.removeClass('selected');
	
				selectedOptions.each(function () {
					$(this).prop('selected', true).addClass('selected');
					var index = $(this).index();
					allLiItems.eq(index).addClass('selected');
				});
	
				slugs = selectedOptions.map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);
			}
		} else {
			// **Single select logic**
			slugs = selectedOptions.data('slug') ? [selectedOptions.data('slug')] : [];
		}
	
		var slugString = slugs.length > 0 ? slugs.join(',') : '';
	
		// **Force unselect checkboxes visually**
		/* allOptions.each(function () {
			var $option = $(this);
			if (!$option.prop('selected')) {
				$option.removeClass('selected');
			}
		}); */
	
		// **Update pagination and filters**
		if ($('.awsm-job-listings').length > 0) {
			$rootWrapper.find('.awsm-job-no-more-jobs-get').hide();
		}
	
		setPaginationBase($rootWrapper, currentSpec, slugString);
	
		// **Update the URL**
		if (awsmJobsPublic.deep_linking.spec) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery(currentSpec, slugString, $paginationBase.val());
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

	$(filterSelector + ' .awsm-filter-checkbox').on('change', function(e) { 
		var selectedFilters = {};
		var slugs = [];  // Initialize an array to collect slugs
		var $elem = $(this);
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var currentSpec = $elem.parents('.awsm-filter-list-item').data('filter'); 
	
		// Loop through checked checkboxes and build selectedFilters and slugs array
		$('.awsm-filter-checkbox:checked').each(function() {
			var taxonomy = $(this).data('taxonomy');
			var termId = $(this).data('term-id');
			var slug = $(this).data('slug'); // Get the slug from the checkbox
	
			// Add the slug to the slugs array if it exists
			if (slug) {
				slugs.push(slug);
			}
	
			// Populate the selectedFilters object
			if (!selectedFilters[taxonomy]) {
				selectedFilters[taxonomy] = [];
			}
			selectedFilters[taxonomy].push(termId);
		});
	
		// Convert slugs array to a comma-separated string
		var slugString = slugs.length > 0 ? slugs.join(',') : '';
	
		// Handle deep linking
		if (awsmJobsPublic.deep_linking.spec) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery(currentSpec, slugString, $paginationBase.val()); // Use the comma-separated slugString
		}
		
		// Apply the job filters
		awsmJobFilters($rootWrapper);
	});
	
	/* ========== Job Listings Load More ========== */

	$(wrapperSelector).on('click', '.awsm-jobs-pagination .awsm-load-more-btn, .awsm-jobs-pagination a.page-numbers', function(e) {
		e.preventDefault(); 
		var $triggerElem = $(this); 
		var isDefaultPagination = $triggerElem.hasClass('awsm-load-more-btn');
		var paged = 1;
		var wpData = [];

		var $mainContainer 		  = $triggerElem.parents(rootWrapperSelector);
		var $listingsContainer 	  = $mainContainer.find(wrapperSelector);
		var $listingsrowContainer = $listingsContainer.find(sectionSelector); 

		var $paginationWrapper   = $triggerElem.parents('.awsm-jobs-pagination');
		var listings 			 = $listingsContainer.data('listings');
		var specs				 = $listingsContainer.data('specs');
		var lang 				 = $listingsContainer.data('lang');
		var searchQuery 		 = $listingsContainer.data('search');
		var selected_terms 		 = $listingsContainer.data('awsm-selected-terms'); 

		if (isDefaultPagination) {
			$triggerElem.prop('disabled', true);
			paged = $triggerElem.data('page');
			paged = (typeof paged == 'undefined') ? 1 : paged;
		} else {
			$triggerElem.parents('.page-numbers').find('.page-numbers').removeClass('current').removeAttr('aria-current');
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

		var specsList = {}; 
		$filterForm.find('.awsm-filter-checkbox:checked').each(function () { 
			var $checkbox = $(this);
			var taxonomy = $checkbox.data('taxonomy'); // Get taxonomy from data attribute
			var termId = $checkbox.data('term-id'); // Get term ID from data attribute
	
			if (taxonomy && termId) {
				if (!specsList[taxonomy]) {
					specsList[taxonomy] = []; // Initialize array for this taxonomy
				}
				specsList[taxonomy].push(termId); // Add term ID to the array
			}
		});

		for (var taxonomy in specsList) {
			if (specsList.hasOwnProperty(taxonomy)) {
				specsList[taxonomy].forEach(function (termId) {
					wpData.push({
						name: `awsm_job_specs_list[${taxonomy}][]`, // Add taxonomy as part of the key
						value: termId
					});
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
		
		if (selected_terms) {
			if (typeof selected_terms === 'string') {
				try {
					// Parse the JSON string into an object
					selected_terms = JSON.parse(selected_terms);
				} catch (error) {
					console.error("Failed to parse selected_terms JSON:", error);
					selected_terms = {}; // Fallback to an empty object
				}
			}
		
			// Push to wpData
			wpData.push({
				name: 'awsm-selected-terms',
				value: JSON.stringify(selected_terms) // Send as JSON string
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
					$listingsrowContainer.append(data);
				} else {
					/* $listingsContainer.html(data); */
					$listingsrowContainer.html(data);
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
				},
				multiple: {
					separator: '... ',      // Items separator updated.
					keepMenuOpen: true,     // Keep the menu open after selection.
					maxLabelEntries: 1      // Limit the number of selected items to 1.
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
