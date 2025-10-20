jQuery(document).ready(function($) {
    $('form#aigq-upload-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = new FormData(form[0]);

        // Add action and nonce to formData
        formData.append('action', 'aigq_upload_audio');
        formData.append('nonce', aigq_ajax.nonce);

        // Disable the submit button and show a processing message
        form.find('input[type="submit"]').prop('disabled', true);
        $('#aigq-upload-status').html('Processing...').show();

        $.ajax({
            type: 'POST',
            url: aigq_ajax.ajax_url,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Redirect to the edit page
                    window.location.href = response.data.redirect_url;
                } else {
                    // Display the error message
                    $('#aigq-upload-status').html(response.data.message);
                    form.find('input[type="submit"]').prop('disabled', false);
                }
            },
            error: function() {
                $('#aigq-upload-status').html('An error occurred.');
                form.find('input[type="submit"]').prop('disabled', false);
            }
        });
    });
});
