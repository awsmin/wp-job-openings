/* global awsmJobsPublic */

'use strict';

jQuery(document).ready(function($) {

	// =============== Job Views ===============
	var jobId = Number(awsmJobsPublic.job_id);
	if (jobId && ! isNaN(jobId)) {
		$.post(awsmJobsPublic.ajaxurl, {
			action: 'awsm_view_count',
			'awsm_job_id': jobId
		});
	}

	// ========== Job Aplication Form ==========
	var $applicationForm = $('#awsm-application-form');
	var $applicationMessage = $('.awsm-application-message');
	var $submitBtn = $('#awsm-application-submit-btn');
	var successClass = 'awsm-success-message';
	var errorClass = 'awsm-error-message';
	var submitBtnText = $submitBtn.val();
	var submitBtnResText = $submitBtn.data('responseText');

	$applicationForm.validate({
		errorElement: 'div',
		errorClass: 'awsm-job-form-error',
		errorPlacement: function(error, element) {
			error.appendTo(element.parents('.awsm-job-form-group'));
		}
	});

	$applicationForm.submit(function(event) {
		$applicationMessage.hide();
		event.preventDefault();
		var proceed = $applicationForm.valid();
		var maxSize = awsmJobsPublic.wp_max_upload_size;
		if (proceed) {
			var form = $('#awsm-application-form')[0];
			var fileCheck = true;
			var $fileControl = $('.awsm-form-file-control');
			if ($fileControl.length > 0) {
				$('.awsm-form-file-control').each(function() {
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
				var formData = new FormData(form);
				$applicationMessage
					.removeClass(successClass + ' ' + errorClass)
					.hide();
				$submitBtn.prop('disabled', true).val(submitBtnResText);
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
							} else {
								if (response.success.length > 0) {
									$applicationForm[0].reset();
									className = successClass;
									msgArray = response.success;
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
						$submitBtn.prop('disabled', false).val(submitBtnText);
					});
			}
		}
	});
});
