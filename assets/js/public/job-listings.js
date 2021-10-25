/* global awsmJobsPublic */

'use strict';

jQuery(function($) {
	var rootWrapperSelector = '.awsm-job-wrap';
	var wrapperSelector = '.awsm-job-listings';

	/* ========== Job Search and Filtering ========== */

	var filterSelector = '.awsm-filter-wrap';
	var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	var triggerFilter = true;

	function awsmJobFilters($rootWrapper) {
		var $wrapper = $rootWrapper.find(wrapperSelector);
		var $filterForm = $rootWrapper.find(filterSelector + ' form');
		var formData = $filterForm.serializeArray();
		var listings = $wrapper.data('listings');
		var specs = $wrapper.data('specs');
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
		awsmJobFilters($rootWrapper);
		if (awsmJobsPublic.deep_linking.search) {
			updateQuery('jq', searchQuery);
		}
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

	var updateQuery = function(key, value) {
		var queryString = document.location.search;
		var param = key + '=' + value;
		var modQueryString = '?' + param;

		if (queryString) {
			var regEx = new RegExp('([?&])' + key + '[^&]*');
			if (queryString.match(regEx) !== null) {
				modQueryString = queryString.replace(regEx, '$1' + param);
			} else {
				modQueryString = queryString + '&' + param;
			}
		}
		window.history.replaceState({}, '', currentUrl + modQueryString);
	};

	$(filterSelector + ' .awsm-filter-option').on('change', function(e) {
		e.preventDefault();
		var $elem = $(this);
		var $selected = $elem.find('option:selected');
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var currentSpec = $elem.parents('.awsm-filter-item').data('filter');
		var slug = $selected.data('slug');
		slug = typeof slug !== 'undefined' ? slug : '';
		awsmJobFilters($rootWrapper);
		if (awsmJobsPublic.deep_linking.spec) {
			updateQuery(currentSpec, slug);
		}
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

	$(wrapperSelector).on('click', '.awsm-load-more-btn', function(e) {
		e.preventDefault();
		var $button = $(this);
		var $mainContainer = $button.parents(rootWrapperSelector);
		var $listingsContainer = $mainContainer.find(wrapperSelector);
		$button.prop('disabled', true);
		var wpData = [];
		var paged = $button.data('page');
		paged = (typeof paged == 'undefined') ? 1 : paged;
		var listings = $listingsContainer.data('listings');
		var specs = $listingsContainer.data('specs');
		var language = $listingsContainer.data('language');
		var searchQuery = $listingsContainer.data('search');

		// filters
		var $filterForm = $mainContainer.find(filterSelector + ' form');
		if (filterCheck($filterForm)) {
			var $filterOption = $filterForm.find('.awsm-filter-option');
			wpData = $filterOption.serializeArray();
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
		if (typeof language !== 'undefined') {
			wpData.push({
				name: 'language',
				value: language
			});
		}
		if (typeof searchQuery !== 'undefined') {
			wpData.push({
				name: 'jq',
				value: searchQuery
			});
		}

		// now, handle ajax
		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			data: $.param(wpData),
			type: 'POST',
			beforeSend: function() {
				$button.text(awsmJobsPublic.i18n.loading_text);
			}
		}).done(function(data) {
			if (data) {
				$button.parents('.awsm-load-more-main').remove();
				$listingsContainer.append(data);
			} else {
				$button.remove();
			}
			$(document).trigger('awsmjobs_load_more', [ $button, data ]);
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
					var $input = $(selectric.elements.input);
					$(select).attr('id', 'selectric-' + id);
					$input.attr('id', id);
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
});
