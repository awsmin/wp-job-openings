/* global awsmJobsPublic */

'use strict';

jQuery(document).ready(function($) {
	var awsmJobs = window.awsmJobs = window.awsmJobs || {};

	// =============== Job Views ===============
	var jobId = Number(awsmJobsPublic.job_id);
	if (jobId && ! isNaN(jobId)) {
		$.post(awsmJobsPublic.ajaxurl, {
			action: 'awsm_view_count',
			'awsm_job_id': jobId
		});
	}

	// ========== Job Application Form ==========
	var $applicationForm = $('.awsm-application-form');

	awsmJobs.submitApplication = function($form, data) {
		data = typeof data !== 'undefined' ? data : {};
		var $submitBtn = $form.find('.awsm-application-submit-btn');
		var $applicationMessage = $form.parents('.awsm-job-form-inner').find('.awsm-application-message');
		var submitBtnText = $submitBtn.val();
		var submitBtnResText = $submitBtn.data('responseText');
		var successClass = 'awsm-success-message';
		var errorClass = 'awsm-error-message';

		$('.awsm-application-message').hide();

		var form = $form[0];
		var fileCheck = true;
		var $fileControl = $form.find('.awsm-form-file-control');
		var maxSize = awsmJobsPublic.wp_max_upload_size;
		
		if ($fileControl.length > 0) {
			$fileControl.each(function() {
				var $fileField = $(this);
				var fileSize = (typeof $fileField.prop('files')[0] !== 'undefined' && $fileField.prop('files')[0]) ? $fileField.prop('files')[0].size : 0;
				if (fileSize > maxSize) {
					fileCheck = false;
				}
			});
		}
		
		if (fileCheck === false) {
			$applicationMessage
				.addClass(errorClass)
				.html(awsmJobsPublic.i18n.form_error_msg.file_validation)
				.fadeIn();
			return;
		}

		$applicationMessage
			.removeClass(successClass + ' ' + errorClass)
			.hide();
		$submitBtn.prop('disabled', true).val(submitBtnResText).addClass('awsm-application-submit-btn-disabled');

		var formData = new FormData(form);
		
		if ('fields' in data && Array.isArray(data.fields)) {
			$.each(data.fields, function(index, field) {
				if ('name' in field && 'value' in field) {
					formData.append(field.name, field.value);
				}
			});
		}

		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			cache: false,
			contentType: false,
			processData: false,
			data: formData,
			dataType: 'json',
			type: 'POST'
		})
		.done(function(response) {
			if (response) {
				var className = 'awsm-default-message';
				var msg = '';
				var msgArray = [];
				if (response.error.length > 0) {
					className = errorClass;
					msgArray = response.error;
					$form.trigger('awsmjobs_application_failed', [ response ]);
				} else {
					if (response.success.length > 0) {
						$form[0].reset();
						$form.find('select.awsm-job-form-field').selectric('refresh');
						className = successClass;
						msgArray = response.success;
						$form.trigger('awsmjobs_application_submitted', [ response ]);
					}
				}
				$(msgArray).each(function(index, value) {
					msg += '<p>' + value + '</p>';
				});
				$applicationMessage
					.addClass(className)
					.html(msg)
					.fadeIn();
			}
		})
		.fail(function(xhr) {
			$applicationMessage
				.addClass(errorClass)
				.html(awsmJobsPublic.i18n.form_error_msg.general)
				.fadeIn();
			console.log(xhr);
		})
		.always(function() {
			$submitBtn.prop('disabled', false).val(submitBtnText).removeClass('awsm-application-submit-btn-disabled');
			function getRecaptchaWidgetId($widget) {
				var $textarea = $widget.find('textarea.g-recaptcha-response');
				if ($textarea.length > 0) {
					var textareaId = $textarea.attr('id');
					if (textareaId) {
						var widgetId = textareaId.replace('g-recaptcha-response-', '');
						if (!isNaN(widgetId) && widgetId !== '') {
							return parseInt(widgetId);
						}
					}
				}
				return null;
			}
			// Only reset visible reCAPTCHA (v2 checkbox), NOT v3 or v2 invisible

			if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.reset === 'function') {
				var $recaptchaWidget = $form.find('.g-recaptcha');
				if ($recaptchaWidget.length > 0 && $recaptchaWidget.is(':visible')) {
					if (typeof awsmJobsRecaptcha === 'undefined' || 
						(awsmJobsRecaptcha.type !== 'v3' && awsmJobsRecaptcha.type !== 'v2_invisible')) {
						try {
							var widgetId = getRecaptchaWidgetId($recaptchaWidget);
							if (widgetId !== null) {
								grecaptcha.reset(widgetId);
							} else {
								grecaptcha.reset();
							}
						} catch(e) {
							console.log('reCAPTCHA reset error:', e);
						}
					}
				}
			}
			// Reset Turnstile
			if (typeof turnstile !== 'undefined' && typeof turnstile.reset === 'function') {
				try {
					var $turnstileWidget = $form.find('.cf-turnstile');
					if ($turnstileWidget.length > 0) {
						turnstile.reset($turnstileWidget[0]);
					}
					else{
						turnstile.reset();
					}
				} catch(e) {
					console.log('Turnstile reset error:', e);
				}
			}
			// Reset hCaptcha
			if (typeof hcaptcha !== 'undefined' && typeof hcaptcha.reset === 'function') {
				try {
					var $hcaptchaWidget = $form.find('.h-captcha');
					if ($hcaptchaWidget.length > 0) {
						var $iframe = $hcaptchaWidget.find('iframe[data-hcaptcha-widget-id]');
            			var hcaptchaWidgetId = $iframe.attr('data-hcaptcha-widget-id');
						if (typeof hcaptchaWidgetId !== 'undefined') {
							hcaptcha.reset(hcaptchaWidgetId);
						} else {
							hcaptcha.reset();
						}
					}
				} catch(e) {
					console.log('hCaptcha reset error:', e);
				}
			}
			// Reset recaptcha v2 invisible
			if (typeof awsmJobsRecaptcha !== 'undefined' && awsmJobsRecaptcha.type === 'v2_invisible') {
				if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.reset === 'function') {
					try {
						var $recaptchaBadge = $('.grecaptcha-badge');
						if ($recaptchaBadge.length > 0) {
							var $textarea = $('textarea[name="g-recaptcha-response"]');
							if ($textarea.length > 0) {
								var textareaId = $textarea.attr('id');
								if (textareaId) {
									var widgetId = textareaId.replace('g-recaptcha-response-', '');
									if (!isNaN(widgetId) && widgetId !== '') {
										grecaptcha.reset(parseInt(widgetId));
									}
								}
							}
						}
					} catch(e) {
						console.log('reCAPTCHA v2 invisible reset error:', e);
					}
				}
			}
		});
	};

	awsmJobs.executeRecaptcha = function($form) {
		var $applicationMessage = $form.parents('.awsm-job-form-inner').find('.awsm-application-message');
		
		if (typeof awsmJobsRecaptcha === 'undefined' || typeof grecaptcha === 'undefined') {
			awsmJobs.submitApplication($form);
			return;
		}

		var siteKey = awsmJobsRecaptcha.site_key;
		var action = awsmJobsRecaptcha.action;

		// Add try-catch wrapper
		try {
			grecaptcha.ready(function() {
				try {
					grecaptcha.execute(siteKey, { action: action }).then(function(token) {
						var $existingToken = $form.find('input[name="g-recaptcha-response"]');
						if ($existingToken.length > 0) {
							$existingToken.remove();
						}
						
						$form.append('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
						
						var $tokenField = $form.find('input[name="g-recaptcha-response"]');
						var tokenValue = $tokenField.val();
						
						if (!tokenValue || tokenValue === '') {
							$applicationMessage
								.addClass('awsm-error-message')
								.html('<p>' + awsmJobsRecaptcha.error_msg + '</p>') 
								.fadeIn();
							
							var $submitBtn = $form.find('.awsm-application-submit-btn');
							var submitBtnText = $submitBtn.data('originalText') || $submitBtn.val();
							$submitBtn.prop('disabled', false).val(submitBtnText).removeClass('awsm-application-submit-btn-disabled');
							return;
						}
						
						awsmJobs.submitApplication($form);
					}).catch(function(error) {
						
						var errorMsg = awsmJobsRecaptcha.error_msg || awsmJobsPublic.i18n.form_error_msg.captcha_failed;
						
						$applicationMessage
							.addClass('awsm-error-message')
							.html('<p>' + errorMsg + '</p>') 
							.fadeIn();
						
						var $submitBtn = $form.find('.awsm-application-submit-btn');
						var submitBtnText = $submitBtn.data('originalText') || $submitBtn.val();
						$submitBtn.prop('disabled', false).val(submitBtnText).removeClass('awsm-application-submit-btn-disabled');
					});
				} catch(error) {
					
					var errorMsg = awsmJobsRecaptcha.error_msg || awsmJobsPublic.i18n.form_error_msg.captcha_failed;
					
					$applicationMessage
						.addClass('awsm-error-message')
						.html('<p>' + errorMsg + '</p>') 
						.fadeIn();
					
					var $submitBtn = $form.find('.awsm-application-submit-btn');
					var submitBtnText = $submitBtn.data('originalText') || $submitBtn.val();
					$submitBtn.prop('disabled', false).val(submitBtnText).removeClass('awsm-application-submit-btn-disabled');
				}
			});
		} catch(error) {
			
			var errorMsg = awsmJobsRecaptcha.error_msg || awsmJobsPublic.i18n.form_error_msg.captcha_failed;			
			$applicationMessage
				.addClass('awsm-error-message')
				.html('<p>' + errorMsg + '</p>') 
				.fadeIn();
			
			var $submitBtn = $form.find('.awsm-application-submit-btn');
			var submitBtnText = $submitBtn.data('originalText') || $submitBtn.val();
			$submitBtn.prop('disabled', false).val(submitBtnText).removeClass('awsm-application-submit-btn-disabled');
		}
	};

	var enableValidation = 'jquery_validation' in awsmJobsPublic.vendors && awsmJobsPublic.vendors.jquery_validation;

	if (enableValidation) {
		$applicationForm.each(function() {
			var $form = $(this);
			$form.validate({
				errorElement: 'div',
				errorClass: 'awsm-job-form-error',
				errorPlacement: function(error, element) {
					error.appendTo(element.parents('.awsm-job-form-group'));
				}
			});
		});
	}

	$applicationForm.on('submit', function(event) {
		event.preventDefault();
		var $form = $(this);
		var proceed = true;
		
		if (enableValidation) {
			proceed = $form.valid();
		}
		
		if (proceed) {
			if (typeof awsmJobsRecaptcha !== 'undefined') {
				awsmJobs.executeRecaptcha($form);
			} else {
				awsmJobs.submitApplication($form);
			}
		}
	});
	// Job Application Form - In-App Browsers support.
	if ($('.awsm-application-form .awsm-form-file-control').length  > 0) {
		var userAgent = navigator.userAgent;
		if (typeof userAgent !== 'undefined') {
			var isFBAppBrowser = (userAgent.indexOf('FBAN') > -1) || (userAgent.indexOf('FBAV') > -1) || (userAgent.indexOf('Instagram') > -1);
			if (isFBAppBrowser) {
				$('.awsm-application-form .awsm-form-file-control').removeAttr('accept');
			}
		}
	}
});
