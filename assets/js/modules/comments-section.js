jQuery(document).ready(function ($) {
    $('#commentform').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        let form = $(this);
        let formData = form.serialize();

        $.ajax({
            url: ajax_comments.ajaxurl,
            type: 'POST',
            data: formData + '&action=submit_comment&nonce=' + ajax_comments.nonce,
            beforeSend: function () {
                $('#submit').prop('disabled', true);
                $('#comment-message').removeClass('bg-red-100 bg-green-100 text-red-700 text-green-700').addClass('hidden');
            },
            success: function (response) {
                if (response.success) {
                    $('#comment-list').append(response.data.comment);
                    $('#comment-message').html('Your comment has been successfully submitted!')
                        .removeClass('hidden')
                        .addClass('bg-green-100 text-green-700');
                    $('#commentform')[0].reset();

                    setTimeout(function () {
                        $('#comment-message').addClass('hidden');
                    }, 10000);
                } else {
                    $('#comment-message').html(response.data.message)
                        .removeClass('hidden')
                        .addClass('bg-red-100 text-red-700');

                    setTimeout(function () {
                        $('#comment-message').addClass('hidden');
                    }, 10000);
                }
            },
            error: function (xhr, status, error) {
                $('#comment-message').html('There was an error submitting your comment. Please try again.')
                    .removeClass('hidden')
                    .addClass('bg-red-100 text-red-700');

                setTimeout(function () {
                    $('#comment-message').addClass('hidden');
                }, 10000);
            },
            complete: function () {
                $('#submit').prop('disabled', false);
            }
        });
    });
});
