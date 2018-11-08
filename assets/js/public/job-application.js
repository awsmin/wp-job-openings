jQuery(document).ready(function ($) { 
  // =============== Job Views ===============
  var job_id = Number(awsmJobsPublic.job_id);
  if(job_id && ! isNaN(job_id)) {
    $.post(awsmJobsPublic.ajaxurl, { action: 'awsm_view_count', awsm_job_id: job_id });
  }

  // ========== Job Aplication Form ==========
  var $application_form = $("#awsm-application-form");
  var $application_message = $(".awsm-application-message");
  var $submit_btn = $('#awsm-application-submit-btn');
  var success_class = "awsm-success-message";
  var error_class = "awsm-error-message";
  var submit_btn_text = $submit_btn.val();
  var submit_btn_res_text = $submit_btn.data('responseText');

  $application_form.validate({
    errorElement: "div",
    errorClass: "awsm-job-form-error",
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".awsm-job-form-group"));
    }
  });

  $application_form.submit(function (event) {
    $application_message.hide();
    event.preventDefault();
    var proceed = $application_form.valid();
    var maxSize = awsmJobsPublic.wp_max_upload_size;
    if (proceed) {
      var container = $(".awsm-job-form");
      var field = $("input[name='awsm_file']", container);
      var form = $("#awsm-application-form")[0];
      var file_check = true;
      var $file_control = $('.awsm-form-file-control');
      if( $file_control.length > 0 ) {
        $('.awsm-form-file-control').each(function() {
          var $file_field = $(this);
          var file_size = (typeof $file_field.prop("files")[0] !== 'undefined') ? $file_field.prop("files")[0].size : 0;
          if( file_size > maxSize ) {
            file_check = false;
          }
        });
      }
      if (file_check === false) {
        $application_message
          .addClass(error_class)
          .html(awsmJobsPublic.i18n.form_error_msg.file_validation)
          .fadeIn();
      } else {
        var form_data = new FormData(form);
        $application_message
          .removeClass(success_class + " " + error_class)
          .hide();
        $submit_btn.prop('disabled', true).val(submit_btn_res_text);
        $.ajax({
            url: awsmJobsPublic.ajaxurl,
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            dataType: "json",
            type: "POST"
          })
          .done(function (response) {
            if (response) {
              var class_name = "awsm-default-message";
              var msg = "";
              var msg_array = [];
              if (response.error.length > 0) {
                class_name = error_class;
                msg_array = response.error;
              } else {
                if (response.success.length > 0) {
                  $application_form[0].reset();
                  class_name = success_class;
                  msg_array = response.success;
                }
              }
              $(msg_array).each(function (index, value) {
                msg += "<p>" + value + "</p>";
              });
              $application_message
                .addClass(class_name)
                .html(msg)
                .fadeIn();
            }
          })
          .fail(function (xhr) {
            $application_message
              .addClass(error_class)
              .html(awsmJobsPublic.i18n.form_error_msg.general)
              .fadeIn();
            console.log(xhr);
          })
          .always(function () {
            $submit_btn.prop('disabled', false).val(submit_btn_text);
          });
      }
    }
  });
});