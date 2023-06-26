/* global awsmJobsAdmin, Clipboard */

'use strict';

/*================ Setup screen ================*/
jQuery(window).on('load', function() {
	jQuery('.awsm-job-setup').addClass('loaded');
});

jQuery(document).ready(function($) {
	var jobsAdminMain = window.awsmJobsAdminMain = window.awsmJobsAdminMain || {};

	/**
	 * for generic select2 initialization
	 */
	jobsAdminMain.selectControl = function($elem, placeholder) {
		placeholder = (typeof placeholder !== 'undefined') ? placeholder : '';
		var options = {
			minimumResultsForSearch: 25,
			theme: 'awsm-job'
		};
		if (placeholder.length > 0) {
			options.placeholder = placeholder;
		}
		$elem.awsmSelect2(options);
	};

	/**
	 * for select2 initialization with tags input
	 */
	jobsAdminMain.tagSelect = function($elem, dropdownHidden, additionalConfig) {
		dropdownHidden = (typeof dropdownHidden !== 'undefined') ? dropdownHidden : true;
		additionalConfig = typeof additionalConfig !== 'undefined' ? additionalConfig : {};
		if ($elem.length > 0) {
			var config = {
				tags: true,
				tokenSeparators: [ ',' ],
				theme: 'awsm-job',
				dropdownCssClass: (dropdownHidden ? 'awsm-hidden-control' : 'awsm-select2-dropdown-control')
			};
			jQuery.extend(config, additionalConfig);
			$elem.awsmSelect2(config);
		}
	};

	/*================ General ================*/

	$('.awsm-check-control-field').on('change', function() {
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

	$('.awsm-check-toggle-control').on('change', function() {
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

	$('.awsm-jobs-colorpicker-field').wpColorPicker();

	jobsAdminMain.selectControl($('.awsm-select-page-control'), awsmJobsAdmin.i18n.select2_no_page);
	jobsAdminMain.selectControl($('.awsm-select-control'));

	/*================ Job Expiry ================*/

	var dateToday = new Date();
	$('#awsm-jobs-datepicker').datepicker({
		altField: '#awsm-jobs-datepicker-alt',
		altFormat: 'yy-mm-dd',
		showOn: 'both',
		buttonText: '',
		buttonImage: awsmJobsAdmin.plugin_url + '/assets/img/calendar-alt.svg',
		buttonImageOnly: true,
		changeMonth: true,
		numberOfMonths: 1,
		minDate: dateToday
	});

	/*================ Job Specifications ================*/

	function customTagsMatcher(params, data) {

		// If there are no search terms, return all of the data
		if (params.term.trim() === '') {
			return data;
		}

		// Do not display the item if there is no 'text' property
		if (typeof data.id === 'undefined') {
			return null;
		}

		if (data.id.toLowerCase() === params.term.toLowerCase()) {
			var modifiedData = $.extend({}, data, true);
			return modifiedData;
		}

		return null;
	}

	jobsAdminMain.tagSelect($('.awsm_jobs_filter_tags'), true, {
		matcher: customTagsMatcher,
		templateResult: function(val) {
			return val.id;
		}
	});

	jobsAdminMain.tagSelect($('.awsm_job_specification_terms'), false, {
		createTag: function(params) {
			var currentId = $.trim(params.term);
			if (currentId === '') {
				return null;
			}
			if (! _.isNaN(currentId) && currentId.length > 0) {
				currentId = 'awsm-term-id-' + currentId;
			}
			return {
				id: currentId,
				text: params.term,
				newItem: true
			};
		}
	});

	var specRegEx = new RegExp('^([a-z0-9]+(-|_))*[a-z0-9]+$');
	var $specWrapper = $('#awsm-job-specifications-options-container');

	var tlData = { 'а': 'a', 'А': 'a', 'б': 'b', 'Б': 'B', 'в': 'v', 'В': 'V', 'ґ': 'g', 'г': 'g', 'Г': 'G', 'д': 'd', 'Д': 'D', 'е': 'e', 'Е': 'E', 'є': 'ye', 'э': 'e', 'Э': 'E', 'и': 'i', 'і': 'i', 'ї': 'yi', 'й': 'i', 'И': 'I', 'Й': 'I', 'к': 'k', 'К': 'K', 'л': 'l', 'Л': 'L', 'м': 'm', 'М': 'M', 'н': 'n', 'Н': 'N', 'о': 'o', 'О': 'O', 'п': 'p', 'П': 'P', 'р': 'r', 'Р': 'R', 'с': 's', 'С': 'S', 'т': 't', 'Т': 'T', 'у': 'u', 'У': 'U', 'ф': 'f', 'Ф': 'F', 'х': 'h', 'Х': 'H', 'ц': 'c', 'ч': 'ch', 'Ч': 'CH', 'ш': 'sh', 'Ш': 'SH', 'щ': 'sch', 'Щ': 'SCH', 'ж': 'zh', 'Ж': 'ZH', 'з': 'z', 'З': 'Z', 'Ъ': '\'', 'ь': '\'', 'ъ': '\'', 'Ь': '\'', 'ы': 'i', 'Ы': 'I', 'ю': 'yu', 'Ю': 'YU', 'я': 'ya', 'Я': 'Ya', 'ё': 'yo', 'Ё': 'YO', 'Ц': 'TS' };

	// Spec icons select
	var iconData = [ {
		id: '',
		text: ''
	} ];

	function formatIconSelectState(state) {
		if (! state.id) {
			return state.text;
		}
		var $state = $('<span><i class="awsm-job-icon-' + state.id + '"></i> ' + state.id + '</span>');
		return $state;
	}

	function transliterate(text) {
		var chars = text.split('');
		return chars.map(function(char) {
			return (char in tlData) ? tlData[char] : char;
		}).join('');
	}

	jobsAdminMain.iconSelect = function($elem, data) {
		var placeholderText = $elem.data('placeholder');
		$elem.awsmSelect2({
			placeholder: {
				id: '',
				text: placeholderText
			},
			allowClear: true,
			data: data,
			templateResult: formatIconSelectState,
			templateSelection: formatIconSelectState,
			theme: 'awsm-job'
		});
	};

	function awsmIconData() {
		$.getJSON(awsmJobsAdmin.plugin_url + '/assets/fonts/awsm-icons.json', function(data) {
			$.each(data.icons, function(index, icon) {
				iconData.push({
					id: icon,
					text: icon
				});
			});
			jobsAdminMain.iconSelect($('.awsm-icon-select-control'), iconData);
		});
	}
	awsmIconData();

	function makeSpecSortable() {
		$('#awsm-repeatable-specifications').sortable({
			items: '.awsm-job-specifications-settings-row',
			axis: 'y',
			handle: '.awsm-specs-drag-control',
			cursor: 'grabbing'
		});
	}
	makeSpecSortable();

	$('.awsm_jobs_filter_tags').on('select2:unselect', function(e) {
		var $row = $(this).parents('.awsm-job-specifications-settings-row');
		var index = $row.data('index');
		var unselectedElem = e.params.data.element;
		var termId = $(unselectedElem).data('termid');
		if (typeof index !== 'undefined' && _.isNumber(termId)) {
			$row.append('<input type="hidden" class="awsm_jobs_remove_filter_tags" name="awsm_jobs_filter[' + index + '][remove_tags][]" value="' + termId + '" />');
		}
	});

	$('.awsm-add-filter-row').on('click', function(e) {
		e.preventDefault();
		var enableRow = true;
		$('.awsm-job-specifications-settings-row .awsm-jobs-spec-title').each(function() {
			if ($(this).val().length == 0) {
				$(this).focus();
				enableRow = false;
			}
		});
		if (enableRow) {
			var $wrapper = $('#awsm-repeatable-specifications');
			var next = $wrapper.data('next');
			var specTemplate = wp.template('awsm-job-spec-settings');
			var templateData = { index: next };
			$wrapper.data('next', next + 1);
			$wrapper.find('.awsm_job_specifications_settings_body').append(specTemplate(templateData));
			jobsAdminMain.tagSelect($('.awsm_jobs_filter_tags').last());
			jobsAdminMain.iconSelect($('.awsm-icon-select-control').last(), iconData);
		}
	});

	$('#awsm-repeatable-specifications').on('click', '.awsm-filters-remove-row', function(e) {
		e.preventDefault();
		var $deleteBtn = $(this);
		var $wrapper = $('#awsm-repeatable-specifications');
		var rowSelector = '.awsm-job-specifications-settings-row';
		var next = $(rowSelector).length;
		var taxonomy = $deleteBtn.data('taxonomy');
		next = (typeof next !== 'undefined' && next > 0) ? (next - 1) : 0;
		$wrapper.data('next', next);
		$deleteBtn.parents(rowSelector).remove();
		if (typeof taxonomy !== 'undefined') {
			$wrapper.append('<input type="hidden" name="awsm_jobs_remove_filters[]" value="' + taxonomy + '" />');
		}
	});

	$specWrapper.on('keyup blur', '.awsm-jobs-spec-title', function() {
		var $specElem = $(this);
		var title = $specElem.val();
		var $row = $specElem.parents('.awsm-job-specifications-settings-row');
		if (title.length > 0) {
			title = $.trim(title).replace(/\s+/g, '-').toLowerCase();
			if (! specRegEx.test(title)) {
				var tlText = transliterate(title);
				title = tlText !== title ? tlText : '';
			}
			$row.find('.awsm-jobs-spec-key').val(title);
		}
	});

	$specWrapper.parents('#settings-awsm-settings-specifications').find('form').submit(function(e) {
		if ($specWrapper.is(':visible')) {
			var isValid = true;
			$('.awsm-jobs-error-container').remove();
			$('.awsm-jobs-spec-key').each(function() {
				var key = $(this).val();
				if (! specRegEx.test(key)) {
					isValid = false;
				}
			});
			if (! isValid) {
				e.preventDefault();
				var errorTemplate = wp.template('awsm-job-spec-settings-error');
				var templateData = {isInvalidKey: true};
				$specWrapper.find('.awsm-form-section').append(errorTemplate(templateData));
			}
		}
	});

	/*================ Settings Error Handling ================*/

	$('#awsm-job-settings-wrap input[type="submit"]').on('click', function() {
		var $form = $('#awsm-job-settings-wrap form');
		if ($form.get(0).checkValidity() === false) {
			$('.awsm-jobs-settings-error').removeClass('awsm-hide');

			// Handle accordions.
			$('.awsm-acc-head').addClass('on');
			$('.awsm-acc-content').slideDown('normal');

			$('html, body').animate({
				scrollTop: $('#awsm-job-settings-wrap .awsm-settings-tab-wrapper').offset().top
			}, 600);
		} else {
			$('.awsm-jobs-settings-error').addClass('awsm-hide');
		}
	});

	/*================ Settings Navigation ================*/

	function awsmSubtabToggle($currentSubtab, enableFadeIn) {
		enableFadeIn = (typeof enableFadeIn !== 'undefined') ? enableFadeIn : false;
		var currentTarget = $currentSubtab.data('target');
		var $currentTargetContainer = $(currentTarget);
		if ($currentTargetContainer.length > 0) {
			var $mainTab = $currentSubtab.closest('.awsm-admin-settings');
			$currentTargetContainer.find('[data-required="required"]').prop('required', true);
			$mainTab.find('.awsm-sub-options-container').hide();
			$mainTab.find('.awsm-nav-subtab').removeClass('current');
			$currentSubtab.addClass('current');
			if (enableFadeIn) {
				$currentTargetContainer.fadeIn();
			} else {
				$currentTargetContainer.show();
			}
		}
	}

	var subtabsSelector = '.awsm_current_settings_subtab';
	var $subtabs = $(subtabsSelector);
	if ($subtabs.length > 0) {
		$($subtabs).each(function() {
			var currentSubtabId = $(this).val();
			var $currentSubtab = $('#' + currentSubtabId);
			awsmSubtabToggle($currentSubtab, true);
		});
	}
	$('#awsm-job-settings-wrap').on('click', '.awsm-nav-subtab', function(e) {
		e.preventDefault();
		var $currentSubtab = $(this);
		var currentSubtabId = $currentSubtab.attr('id');
		var $mainTab = $currentSubtab.closest('.awsm-admin-settings');
		if (! $currentSubtab.hasClass('current')) {
			$mainTab.find('[data-required="required"]').prop('required', false);
			awsmSubtabToggle($currentSubtab, true);
			$mainTab.find(subtabsSelector).val(currentSubtabId);
		}
	});

	/*================ Settings Loader ================*/

	$('.awsm-jobs-settings-loader-container').fadeOut(function() {
		$('#awsm-jobs-settings-section').css('visibility', 'visible').addClass('awsm-visible');
	});

	/*================ Settings: Image Upload Field ================*/

	var frame;
	var imgi18n = awsmJobsAdmin.i18n.image_upload;
	$('#awsm-job-settings-wrap').on('click', '.awsm-settings-image-upload-button', function(e) {
		e.preventDefault();
		var $elem = $(this);
		$elem.parent('.awsm-settings-image-field-container').addClass('awsm-settings-image-trigger-active');

		if (! frame) {
			frame = wp.media({
				title: imgi18n.title,
				multiple: false,
				library: {
					type: 'image'
				},
				button: {
					text: imgi18n.btn_text
				}
			});
		}
		var Button = wp.media.view.Button;
		wp.media.view.Button = Button.extend({
		  initialize: function () {
			var options = _.defaults(this.options, this.defaults);
			this.model = new Backbone.Model(options);
			this.listenTo(this.model, 'change', this.render);
		  }
		});

		frame.on('select', function() {
			var attachment = frame.state().get('selection').first().toJSON();
			if (attachment.type === 'image') {
				var imgURL = attachment.url;
				var $imgFieldContainer = $elem.parent('.awsm-settings-image-field-container.awsm-settings-image-trigger-active');
				var $imgWrapper = $imgFieldContainer.find('.awsm-settings-image');
				$imgWrapper.removeClass('awsm-settings-no-image').html('<img src="' + imgURL + '" />');
				$imgFieldContainer.find('.awsm-settings-image-remove-button').removeClass('awsm-hidden-control');
				$imgFieldContainer.find('.awsm-settings-image-upload-button').text(imgi18n.change);
				$imgFieldContainer.find('.awsm-settings-image-field').val(attachment.id);
				$imgFieldContainer.removeClass('awsm-settings-image-trigger-active');
			}
		});

		frame.open();
	});

	$('#awsm-job-settings-wrap').on('click', '.awsm-settings-image-remove-button', function(e) {
		e.preventDefault();
		var $elem = $(this);
		var $imgFieldContainer = $elem.parent('.awsm-settings-image-field-container');
		var $imgWrapper = $imgFieldContainer.find('.awsm-settings-image');
		$imgWrapper.addClass('awsm-settings-no-image').html('<span>' + imgi18n.no_image + '</span>');
		$imgFieldContainer.find('.awsm-settings-image-upload-button').text(imgi18n.select);
		$imgFieldContainer.find('.awsm-settings-image-field').val('');
		$elem.addClass('awsm-hidden-control');
	});

	/*================ Settings: Notifications ================*/

	$('#awsm-jobs-settings-section').on('click', '.awsm-acc-head', function(e) {
		var check = true;
		var $elem = $(this);
		var $switch = $('.awsm-toggle-switch');
		if ($switch.length > 0) {
			if ($switch.is(e.target) || $switch.has(e.target).length > 0) {
				check = false;
			}
		}
		if (check) {
			$('.awsm-acc-head').removeClass('on');
			$('.awsm-acc-content').slideUp('normal');
			if ($elem.next('.awsm-acc-content').is(':hidden') == true) {
				$elem.addClass('on');
				$elem.next('.awsm-acc-content').slideDown('normal');
			}
		}
	});

	/*================ Settings Switch ================*/

	$('.awsm-settings-switch').on('change', function() {
		var $settingsSwitch = $(this);
		var option = $settingsSwitch.attr('id');
		var optionValue = $settingsSwitch.val();
		if (! $settingsSwitch.is(':checked')) {
			optionValue = '';
		}
		var optionsData = {
			action: 'settings_switch',
			nonce: awsmJobsAdmin.nonce,
			option: option,
			'option_value': optionValue
		};
		$.ajax({
			url: awsmJobsAdmin.ajaxurl,
			data: optionsData,
			type: 'POST'
		}).fail(function(xhr) {
			// eslint-disable-next-line no-console
			console.log(xhr);
		});
	});

	/*================ Copy Short code ================*/

	if ($('#awsm-copy-clip').length > 0) {
		var copyCode = new Clipboard('#awsm-copy-clip');
		copyCode.on('success', function(event) {
			event.clearSelection();
			event.trigger.textContent = 'Copied';
			window.setTimeout(function() {
				event.trigger.textContent = 'Copy';
			}, 2000);
		});
		copyCode.on('error', function(event) {
			event.trigger.textContent = 'Press "Ctrl + C" to copy';
			window.setTimeout(function() {
				event.trigger.textContent = 'Copy';
			}, 2000);
		});
	}
	$('#awsm-copy-clip').on('click', function(e) {
		e.preventDefault();
	});

	/*================ Plugin Rating ================*/

	$('.awsm-job-plugin-rating-action').on('click', function(e) {
		e.preventDefault();
		var $elem = $(this);
		var status = $elem.data('status');
		var context = $elem.data('context');
		var data = {
			nonce: awsmJobsAdmin.nonce,
			action: 'awsm_plugin_rating',
			context: context,
			status: status
		};
		$.ajax({
			url: awsmJobsAdmin.ajaxurl,
			data: data,
			type: 'POST'
		}).done(function(response) {
			if (response && response.code === 'success') {
				$('.awsm-job-plugin-rating-wrapper').slideUp('fast');
			}
		});
	});

	$('#awsm-job-setup-form').on('submit', function(e) {
		e.preventDefault();
		$('#awsm-jobs-setup-btn').prop('disabled', true);
		$('.awsm-job-setup-notice').addClass('awsm-hide');
		var $form = $('#awsm-job-setup-form');
		var formData = $form.serializeArray();
		$.ajax({
			url: awsmJobsAdmin.ajaxurl,
			data: formData,
			type: 'POST'
		}).done(function(res) {
			if (typeof res.redirect !== 'undefined' && res.redirect) {
				window.location.replace(res.redirect);
			} else {
				var msg = '';
				$(res.error).each(function(index, value) {
					msg += '<p>' + value + '</p>';
				});
				if (msg.length) {
					$('.awsm-job-setup-notice').html(msg).removeClass('awsm-hide');
				}
			}
		}).always(function() {
			$('#awsm-jobs-setup-btn').prop('disabled', false);
		});
	});
});
