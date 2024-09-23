jQuery(document).ready(function ($) {
    $('#commentform').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        $.ajax({
            url: ajax_comments.ajaxurl,
            type: 'POST',
            data: formData + '&action=submit_comment&nonce=' + ajax_comments.nonce,
            beforeSend: function () {
                $('#submit').prop('disabled', true);
            },
            success: function (response) {
                if (response.success) {
                    $('#comment-list').append(response.data.comment);
                    $('#commentform')[0].reset();
                } else {
                    alert(response.data.message);
                }
            },
            complete: function () {
                $('#submit').prop('disabled', false);
            }
        });
    });
});
