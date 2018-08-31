jQuery(function ($) {
	// ========== Job Filters ==========
	var $filter = $('#awsm-job-filter');
	var $filter_option = $filter.find('.awsm-filter-option');

	function awsm_job_filters() {
		$.ajax({
			url: $filter.attr('action'),
			data: $filter.serialize(),
			type: $filter.attr('method')
		}).done(function (data) {
			$('#awsm-job-response').html(data);
		}).fail(function (xhr) {
			console.log(xhr);
		});
	}

	function filter_check() {
		var check = false;
		if ($filter.length > 0) {
			$filter_option.each(function (i) {
				if ($(this).val().length > 0) {
					check = true;
				}
			});
		}
		return check;
	}

	if (filter_check()) {
		awsm_job_filters();
	}

	$('#awsm-job-filter .awsm-filter-option').on('change', function (e) {
		e.preventDefault();
		awsm_job_filters();
	});

	// ========== Job Listings Load More ==========
	var $content_wrapper = $('#awsm-job-response');
	$content_wrapper.on('click', '.awsm-load-more-btn', function (e) {
		e.preventDefault();
		var $button = $(this);
		$button.prop('disabled', true);
		var paged = $button.data('page');
		paged = (typeof paged == 'undefined') ? 1 : paged;
		var wp_data = 'action=loadmore&paged=' + paged;

		// filters
		if ($filter.length > 0) {
			if (filter_check()) {
				var filter_data = $filter_option.serialize();
				wp_data += ('&' + filter_data);
			}
		}

		// now, handle ajax
		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			data: wp_data,
			type: 'POST',
			beforeSend: function (xhr) {
				$button.text('Loading...');
			}
		}).done(function (data) {
			if (data) {
				$('.awsm-load-more-main').remove();
				$content_wrapper.append(data);
			} else {
				$button.remove();
			}
		}).fail(function (xhr) {
			console.log(xhr);
		});
	});

	/*----- Custom select box - selectric -----*/
	$('.awsm-filter-item select').selectric({
		arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
		customClass: {
			prefix: 'awsm-selectric',
			camelCase: false
		},
	});
});