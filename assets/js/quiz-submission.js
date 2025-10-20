jQuery(document).ready(function($) {
    $('form#aigq-quiz-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var quizId = form.data('quiz-id');
        var answers = form.serialize();

        $.ajax({
            type: 'POST',
            url: aigq_ajax.ajax_url,
            data: {
                action: 'aigq_submit_quiz',
                quiz_id: quizId,
                answers: answers,
                nonce: aigq_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    form.hide();
                    $('#aigq-quiz-results').html(response.data.html).show();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
