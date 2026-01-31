(function($){
    'use strict';

    $(document).ready(function(){

        $('#complaint_report_form').on('submit', function(e){
            e.preventDefault();

            // Check if privacy checkbox is checked
            if( !$('#crf_privacy_policy').is(':checked') ){
                $('#crf_response').html('<div class="alert alert-danger">'+complaint_report_form_ajax_object.msg_privacy_required+'</div>');
                return; // stop submission
            }

            var form = $(this);
            var $submitBtn = form.find('button[type="submit"]');

            // Get the Dropzone instance for this form
            const dzInstance = window.dropzoneInstances['crf_dropzone'];

            grecaptcha.ready(function() {
                grecaptcha.execute(complaint_report_form_ajax_object.recaptcha_site_key, {action: 'complaint_report_form'})
                    .then(function(token) {
                    
                    // Create a FormData object from the form
                    var formData = new FormData(form[0]); 

                    formData.append('action', 'complaint_report_form_handler');
                    formData.append('user_id', complaint_report_form_ajax_object.user_id);
                    formData.append('recaptcha_token', token);

                    // Append Dropzone files if any
                    if(dzInstance) {
                        dzInstance.files.forEach(file => {
                            formData.append('crf_files[]', file);
                            //console.log(file);
                        });
                    }

                    $.ajax({
                        url: complaint_report_form_ajax_object.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,   // prevent jQuery from converting FormData to string
                        contentType: false,   // let browser set multipart/form-data boundary
                        dataType: 'json',

                        // Before sending, show a loading indicator
                        beforeSend: function() {
                            $submitBtn.prop('disabled', true).addClass('disabled');
                            $('#crf_response').html('<div class="alert alert-info">'+complaint_report_form_ajax_object.msg_sending+'</div>');
                        },

                        success: function(response){
                            if(response && typeof response === 'object'){
                                if(response.success){
                                    var message = response.data && response.data.message ? response.data.message : complaint_report_form_ajax_object.msg_success;
                                    $('#crf_response').html('<div class="alert alert-success">'+message+'</div>');

                                    if(response.data.redirect_url){
                                        // Grab values from response
                                        var message_id = response.data.message_id ? response.data.message_id : '';

                                        // Build query string
                                        var queryString = '?message_id=' + encodeURIComponent(message_id);

                                        setTimeout(function(){
                                            window.location.href = response.data.redirect_url + queryString;
                                        }, 1000); // short delay so user sees the success message
                                    }
                                } else {
                                    var message = response.data && response.data.message ? response.data.message : complaint_report_form_ajax_object.msg_error_sending;
                                    $('#crf_response').html('<div class="alert alert-danger">'+message+'</div>');
                                }
                            } else {
                                $('#crf_response').html('<div class="alert alert-danger">'+complaint_report_form_ajax_object.msg_unexpected+'</div>');
                            }
                        },

                        error: function(xhr, status, error){
                            var errMsg = complaint_report_form_ajax_object.msg_network_error;
                            if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                                errMsg = xhr.responseJSON.data.message;
                            } else if(error){
                                errMsg += ' (' + error + ')';
                            }
                            $('#crf_response').html('<div class="alert alert-danger">'+errMsg+'</div>');
                        },

                        complete: function() {
                            // Optional cleanup
                            // Example: console.log('AJAX request completed');
                            $submitBtn.prop('disabled', false).removeClass('disabled');

                            // Wait 3 seconds, then fade out the response over 1 second
                            setTimeout(function() {
                                $('#crf_response').fadeOut(1000, function() {
                                    $(this).html('').show(); // Clear content and reset display
                                });
                            }, 3000);
                        }
                    });

                });
            });
        });

    });

})(jQuery);
