jQuery(function ($) {
	var rootWrapperSelector = '.awsm-job-wrap';
	var wrapperSelector = '.awsm-job-listings';

	/* ========== Job Filters ========== */

	var filterSelector = '.awsm-filter-wrap';
	var currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
	
	function awsmJobFilters($elem) {
		var $wrapper = typeof $elem !== 'undefined' ? $elem.parents(rootWrapperSelector).find(wrapperSelector) : $(wrapperSelector);
		var $filterForm = typeof $elem !== 'undefined' ? $elem.parents(filterSelector).find('form') : $(filterSelector).find('form');
		var formData = $filterForm.serializeArray();
		var listings = $wrapper.data('listings');
		formData.push({ name: 'listings_per_page', value: listings });
		$.ajax({
			url: $filterForm.attr('action'),
			beforeSend: function(xhr) {
				$wrapper.addClass('awsm-jobs-loading');
			},
			data: formData,
			type: $filterForm.attr('method')
		}).done(function (data) {
			$wrapper.html(data);
		}).fail(function (xhr) {
			console.log(xhr);
		}).always(function() {
			$wrapper.removeClass('awsm-jobs-loading');
		});
	}

	function filterCheck($filterForm) {
		var check = false;
		$filterForm = typeof $filterForm !== 'undefined' ? $filterForm : $(filterSelector).find('form');
		if ($filterForm.length > 0) {
			var $filterOption = $filterForm.find('.awsm-filter-option');
			$filterOption.each(function (i) {
				if ($(this).val().length > 0) {
					check = true;
				}
			});
		}
		return check;
	}

	if (filterCheck()) {
		awsmJobFilters();
	}

	var updateQuery = function (key, value) {
		var queryString = document.location.search; 
		var param = key + '=' + value;
		var modQueryString = '?' + param;
		
		if (queryString) {
			var regEx = new RegExp('([\?&])' + key + '[^&]*');
			if (queryString.match(regEx) !== null) {
				modQueryString = queryString.replace(regEx, "$1" + param);
			} else {
				modQueryString = queryString + '&' + param;
			}
		}
		window.history.replaceState({}, "", currentUrl + modQueryString);
	};

	$(filterSelector + ' .awsm-filter-option').on('change', function (e) {
		e.preventDefault();
		var $elem = $(this);
		var currentSpec = $elem.parents('.awsm-filter-item').data('filter');
		var termId = $elem.val();
		updateQuery(currentSpec, termId);
		awsmJobFilters($elem);
	});

	/* ========== Job Listings Load More ========== */

	$(wrapperSelector).on('click', '.awsm-load-more-btn', function (e) {
		e.preventDefault();
		var $button = $(this);
		var $mainContainer = $button.parents(rootWrapperSelector);
		var $listingsContainer = $mainContainer.find(wrapperSelector);
		$button.prop('disabled', true);
		var wp_data = [];
		var paged = $button.data('page');
		paged = (typeof paged == 'undefined') ? 1 : paged;
		var listings = $listingsContainer.data('listings');

		// filters
		var $filterForm = $mainContainer.find(filterSelector + ' form');
		if ($filterForm.length > 0) {
			if (filterCheck($filterForm)) {
				var $filterOption = $filterForm.find('.awsm-filter-option');
				wp_data = $filterOption.serializeArray();
			}
		}

		// taxonomy archives
		if(awsmJobsPublic.is_tax_archive) {
			var taxonomy = $listingsContainer.data('taxonomy');
			var term_id = $listingsContainer.data('termId');
			if(typeof taxonomy !== 'undefined' && typeof term_id !== 'undefined') {
				wp_data.push({
					name: 'awsm_job_spec[' + taxonomy + ']',
					value: term_id
				});
			}
		}

		wp_data.push({ name: 'action', value: 'loadmore' }, { name: 'paged', value: paged });
		if(typeof listings !== 'undefined') {
			wp_data.push({ name: 'listings_per_page', value: listings });
		}

		// now, handle ajax
		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			data: $.param(wp_data),
			type: 'POST',
			beforeSend: function (xhr) {
				$button.text(awsmJobsPublic.i18n.loading_text);
			}
		}).done(function (data) {
			if (data) {
				$button.parents('.awsm-load-more-main').remove();
				$listingsContainer.append(data);
			} else {
				$button.remove();
			}
		}).fail(function (xhr) {
			console.log(xhr);
		});
	});

	/* ========== Custom select box - selectric ========== */
	
	function awsmDropDown($elem) {
		$elem.selectric({
			arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
			customClass: {
				prefix: 'awsm-selectric',
				camelCase: false
			},
		});
	}
	awsmDropDown($('.awsm-job-select-control'));
	awsmDropDown($('.awsm-filter-item select'));
});