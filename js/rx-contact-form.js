jQuery(document).ready(function($) {

    // Handle the form submission
    $('#rx-contact-form').submit(function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        // Check if the form is valid
        if (!this.checkValidity()) {
            $(this).addClass('was-validated');
            return;
        }

        // Disable the submit button and show the loading spinner
        var $submitBtn = $(this).find('button[type="submit"]');
        var $submitBtnPrev = $($submitBtn).html();
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        // Get the form data
        var formData = new FormData(this);

        // Add the action parameter to the form data
        formData.append('action', 'rx_contact_form_submit');

        // Send the AJAX request
        $.ajax({
            url: rx_contact_form_params.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', rx_contact_form_params.nonce);
            },
            success: function(response) {
                $('.alert').fadeOut()
                if (response.success) {
                    // Show the success// message and reset the form
                    $('#rx-contact-form').append('<div class="alert alert-success mt-3">' + response.data + '</div>');
                    $('#rx-contact-form')[0].reset();
                    $('#rx-contact-form').removeClass('was-validated');
                } else {
                    // Show the error message
                    $('#rx-contact-form').append('<div class="alert alert-danger mt-3">' + response.data + '</div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Show the error message
                $('#rx-contact-form').append('<div class="alert alert-danger mt-3">' + errorThrown + '</div>');
            },
            complete: function() {
                // Enable the submit button and hide the loading spinner
                $submitBtn.prop('disabled', false);
                $submitBtn.html($submitBtnPrev);
            }
        });

        return false;
    });
});