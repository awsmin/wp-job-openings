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

		// Hide all the form submission messages.
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
		} else {
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
					// eslint-disable-next-line no-console
					console.log(xhr);
				})
				.always(function() {
					$submitBtn.prop('disabled', false).val(submitBtnText).removeClass('awsm-application-submit-btn-disabled');
				});
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
			awsmJobs.submitApplication($form);
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
