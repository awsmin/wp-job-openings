/**
 * for generic select2 initialization
 */
function awsm_select_control($elem, placeholder) {
	var placeholder = (typeof placeholder !== 'undefined') ? placeholder : '';
	var options = {
		minimumResultsForSearch: 25,
		theme: 'awsm-job'
	}
	if (placeholder.length > 0) {
		options.placeholder = placeholder;
	}
	$elem.select2(options);
}

/**
 * for select2 initialization with tags input
 */
function awsm_tags_select($elem, dropdownHidden) {
	var dropdownHidden = (typeof dropdownHidden !== 'undefined') ? dropdownHidden : true;
	if ($elem.length > 0) {
		$elem.select2({
			tags: true,
			tokenSeparators: [','],
			theme: 'awsm-job',
			dropdownCssClass: (dropdownHidden ? 'awsm-hidden-control' : 'awsm-select2-dropdown-control')
		});
	}
}

jQuery(document).ready(function ($) {

	/*================ General ================*/

	$(".awsm-check-control-field").on('change', function () {
		var $checkControl = $(this);
		var targetSelector = $checkControl.data('reqTarget');
		if (typeof targetSelector !== 'undefined') {
			var $target = $(targetSelector);
			if ($checkControl.is(':checked')) {
				$target.focus();
				$target.prop('required', true);
			} else {
				$target.removeAttr('required');
			}
		}
	});

	$('.awsm-check-toggle-control').on('change', function () {
		var $toggleControl = $(this);
		var targetSelector = $toggleControl.data('toggleTarget');
		if (typeof targetSelector !== 'undefined') {
			var $target = $(targetSelector);
			if ($toggleControl.is(':checked')) {
				var toggle = $toggleControl.data('toggle');
				if (typeof toggle !== 'undefined' && toggle != false) {
					$target.removeClass('awsm-hide');
				} else {
					$target.addClass('awsm-hide');
				}
			} else {
				$target.addClass('awsm-hide');
			}
		}
	});

	awsm_select_control($('.awsm-select-page-control'), awsmJobsAdmin.i18n.select2_no_page);
	awsm_select_control($('.awsm-select-control'));

	/*================ Job Expiry ================*/

	var dateToday = new Date();
	var dates = $("#awsm-jobs-datepicker").datepicker({
		altField: '#awsm-jobs-datepicker-alt',
		altFormat: "yy-mm-d",
		showOn: "both",
		buttonText: '<span class="dashicons dashicons-calendar-alt"></span>',
		changeMonth: true,
		numberOfMonths: 1,
		minDate: dateToday
	});

	/*================ Job Specifications ================*/

	awsm_tags_select($('.awsm_jobs_filter_tags'));
	awsm_tags_select($('.awsm_job_specification_terms'), false);

	// Spec icons select
	var icon_data = [{
		id: '',
		text: ''
	}];

	function format_icon_select_state(state) {
		if (!state.id) {
			return state.text;
		}
		var $state = $('<span><i class="awsm-job-icon-' + state.id + '"></i> ' + state.id + '</span>');
		return $state;
	}

	function awsm_spec_icon_select($elem, data) {
		var placeholder_text = $elem.data('placeholder');
		$elem.select2({
			placeholder: {
				id: '',
				text: placeholder_text
			},
			allowClear: true,
			data: data,
			templateResult: format_icon_select_state,
			templateSelection: format_icon_select_state,
			theme: 'awsm-job'
		});
	}

	function awsm_icon_data() {
		$.getJSON(awsmJobsAdmin.plugin_url + '/assets/fonts/awsm-icons.json', function (data) {
			$.each(data.icons, function (index, icon) {
				icon_data.push({
					id: icon,
					text: icon
				});
			});
			awsm_spec_icon_select($('.awsm-icon-select-control'), icon_data);
		});
	}
	awsm_icon_data();

	$('.awsm_jobs_filter_tags').on('select2:unselect', function (e) {
		var $row = $(this).parents('.awsm_job_specifications_settings_row');
		var index = $row.data('index');
		var unselected = e.params.data.id;
		if (typeof index !== 'undefined' && typeof unselected !== 'undefined') {
			$row.append('<input type="hidden" class="awsm_jobs_remove_filter_tags" name="awsm_jobs_filter[' + index + '][remove_tags][]" value="' + unselected + '" />');
		}
	});

	$('.awsm-add-filter-row').on('click', function (e) {
		e.preventDefault();
		var enable_row = true;
		$('.awsm_job_specifications_settings_row .awsm_jobs_filter_title').each(function () {
			if ($(this).val().length == 0) {
				$(this).focus();
				enable_row = false;
			}
		});
		if (enable_row) {
			var wrapper = '#awsm-repeatable-specifications';
			var $empty_row = $(wrapper + ' .awsm-filter-empty-row');
			var row = $empty_row.clone(true);
			var filter_next = $empty_row.data('next');
			row.find('.awsm_jobs_filter_title').attr('name', 'awsm_jobs_filter[' + filter_next + '][filter]').prop('required', true);
			row.find('.awsm-font-icon-selector').attr('name', 'awsm_jobs_filter[' + filter_next + '][icon]').addClass('awsm-icon-select-control');
			row.find('.awsm-empty-spec-select').attr('name', 'awsm_jobs_filter[' + filter_next + '][tags][]').addClass('awsm_jobs_filter_tags').removeClass('awsm-empty-spec-select');
			$empty_row.data('next', filter_next + 1);
			$(wrapper).find('.awsm_job_specifications_settings_body').append('<tr class="awsm_job_specifications_settings_row">' + row.html() + '</tr>');
			awsm_tags_select($('.awsm_jobs_filter_tags').last());
			awsm_spec_icon_select($('.awsm-icon-select-control').last(), icon_data);
		}
	});

	$('#awsm-repeatable-specifications').on('click', '.awsm-filters-remove-row', function (e) {
		e.preventDefault();
		var $delete_btn = $(this);
		var container_selector = '#awsm-repeatable-specifications';
		var row_selector = '.awsm_job_specifications_settings_row';
		var next = $(row_selector).length;
		var taxonomy = $delete_btn.data('taxonomy');
		next = (typeof next !== 'undefined' && next > 0) ? (next - 1) : 0;
		$(container_selector + ' .awsm-filter-empty-row').data('next', next);
		$delete_btn.parents(row_selector).remove();
		if (typeof taxonomy !== 'undefined') {
			$(container_selector).append('<input type="hidden" name="awsm_jobs_remove_filters[]" value="' + taxonomy + '" />');
		}
	});

	/*================ Settings Navigation ================*/

	$('.awsm-settings-tab-wrapper a').on("click", function (e) {
		e.preventDefault();
		var id = $(e.target).attr("href");
		window.location.hash = id;
		$('.awsm-admin-settings').hide();
		$(id).show();
		$('.nav-tab-active').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
	});

	if (window.location.hash.length > 0) {
		$('.awsm-admin-settings').hide();
		$('.nav-tab-active').removeClass('nav-tab-active');
		$(window.location.hash).show();
		$('a[href="' + window.location.hash + '"]').addClass('nav-tab-active');
	}

	function awsm_subtab_toggle($current_subtab, enableFadeIn) {
		var enableFadeIn = (typeof enableFadeIn !== 'undefined') ? enableFadeIn : false;
		var current_target = $current_subtab.data('target');
		var $current_target_container = $(current_target);
		if ($current_target_container.length > 0) {
			var $main_tab = $current_subtab.closest('.awsm-admin-settings');
			$main_tab.find('.awsm-sub-options-container').hide();
			$main_tab.find('.awsm-nav-subtab').removeClass('current');
			$current_subtab.addClass('current');
			if (enableFadeIn) {
				$current_target_container.fadeIn();
			} else {
				$current_target_container.show();
			}
		}
	}

	var subtabs_selector = '.awsm_current_settings_subtab';
	var $subtabs = $(subtabs_selector);
	if($subtabs.length > 0) {
		$($subtabs).each(function(i) {
			var current_subtab_id = $(this).val();
			var $current_subtab = $('#' + current_subtab_id);
			awsm_subtab_toggle($current_subtab);
		});
	}
	$('#awsm-job-settings-wrap').on('click', '.awsm-nav-subtab', function (e) {
		e.preventDefault();
		var $current_subtab = $(this);
		var current_subtab_id = $current_subtab.attr('id');
		var $main_tab = $current_subtab.closest('.awsm-admin-settings');
		if (!$current_subtab.hasClass('current')) {
			awsm_subtab_toggle($current_subtab, true);
			$main_tab.find(subtabs_selector).val(current_subtab_id);
		}
	});

	/*================ Settings: Notifications ================*/

	$('.awsm-acc-head').click(function (e) {
		var $switch = $('.awsm-toggle-switch');
		if (!$switch.is(e.target) && $switch.has(e.target).length === 0) {
			$('.awsm-acc-head').removeClass('on');
			$('.awsm-acc-content').slideUp('normal');
			if ($(this).next().is(':hidden') == true) {
				$(this).addClass('on');
				$(this).next().slideDown('normal');
			}
		}
	});

	/*================ Settings Switch ================*/

	$('.awsm-settings-switch').on('change', function (e) {
		$settings_switch = $(this);
		var option = $settings_switch.attr('id');
		var option_value = $settings_switch.val();
		if (!$settings_switch.is(':checked')) {
			option_value = '';
		}
		var options_data = {
			action: 'settings_switch',
			nonce: awsmJobsAdmin.nonce,
			option: option,
			option_value: option_value
		};
		$.ajax({
			url: awsmJobsAdmin.ajaxurl,
			data: options_data,
			type: 'POST'
		}).done(function (data) {
			console.log(data);
		}).fail(function (xhr) {
			console.log(xhr);
		});
	});

	/*================ Copy Short code ================*/

	if ($('#awsm-copy-clip').length > 0) {
		var copyCode = new Clipboard('#awsm-copy-clip');
		copyCode.on('success', function (event) {
			event.clearSelection();
			event.trigger.textContent = 'Copied';
			window.setTimeout(function () {
				event.trigger.textContent = 'Copy';
			}, 2000);
		});
		copyCode.on('error', function (event) {
			event.trigger.textContent = 'Press "Ctrl + C" to copy';
			window.setTimeout(function () {
				event.trigger.textContent = 'Copy';
			}, 2000);
		});
	}
	$('#awsm-copy-clip').on('click', function (e) {
		e.preventDefault();
	});
});