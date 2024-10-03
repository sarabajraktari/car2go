jQuery(document).ready(function ($) {
    $('#commentform').off('submit');

    $('#commentform').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        let form = $(this);
        let formData = form.serialize();

        let recaptchaResponse = grecaptcha.getResponse();

        if (recaptchaResponse.length === 0) {
            $('#comment-message').html('Please solve the reCAPTCHA.')
                .removeClass('hidden')
                .addClass('bg-red-100 text-red-700');
            return; 
        }

        if ($('#submit').prop('disabled')) {
            return;
        }

        $.ajax({
            url: ajax_comments.ajaxurl,
            type: 'POST',
            data: formData + '&g-recaptcha-response=' + recaptchaResponse + '&action=submit_comment&nonce=' + ajax_comments.nonce,
            beforeSend: function () {
                $('#submit').prop('disabled', true); 
                $('#comment-message').removeClass('bg-red-100 bg-green-100 text-red-700 text-green-700').addClass('hidden');
            },
            success: function (response) {
                if (response.success) {

                    $('#comment-message').html(response.data.message)
                        .removeClass('hidden')
                        .addClass('bg-green-100 text-green-700');

                    $('#commentform')[0].reset();
                    grecaptcha.reset(); 

                    reloadCommentsSection();

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

                setTimeout(function () {
                    $('#submit').prop('disabled', false);
                }, 5000);
            }
        });
    });

    function reloadCommentsSection() {
        let url = window.location.href; 


        $.ajax({
            url: url, 
            type: 'GET',
            success: function (response) {

                let newComments = $(response).find('#comment-list').html();

                $('#comment-list').html(newComments);

                $(document).trigger('commentsRefreshed');
            },
            error: function (xhr, status, error) {
                console.error('Error reloading comments:', xhr.status, xhr.statusText);
            }
        });
    }

    function initializeCommentRatings() {

        console.log("Checking initial like/dislike status");
        $.each(ajax_comments_rating.comments_status, function (commentID, status) {
            if (status.liked) {
                $('.like-btn[data-comment-id="' + commentID + '"]').addClass('active');
            }
            if (status.disliked) {
                $('.dislike-btn[data-comment-id="' + commentID + '"]').addClass('active');
            }
        });


        $('.like-btn').off('click');
        $('.dislike-btn').off('click');

        $('.like-btn').on('click', function () {
            let commentID = $(this).data('comment-id');
            let likeCountElement = $(this).find('.like-count');
            let dislikeCountElement = $(this).siblings('.dislike-btn').find('.dislike-count'); 
            let likeButton = $(this);
            let dislikeButton = $(this).siblings('.dislike-btn');

            $.ajax({
                url: ajax_comments_rating.ajaxurl,
                type: 'POST',
                data: {
                    action: 'like_comment',
                    comment_id: commentID,
                    nonce: ajax_comments_rating.nonce
                },
                success: function (response) {
                    if (response.success) {

                        likeCountElement.text(response.data.likes_count);
                        dislikeCountElement.text(response.data.dislikes_count);

                        if (likeButton.hasClass('active')) {
                            likeButton.removeClass('active');
                        } else {
                            likeButton.addClass('active');
                            dislikeButton.removeClass('active');
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error in Like AJAX request:', xhr.status, xhr.statusText);
                }
            });
        });

        $('.dislike-btn').on('click', function () {
            let commentID = $(this).data('comment-id');
            let dislikeCountElement = $(this).find('.dislike-count');
            let likeCountElement = $(this).siblings('.like-btn').find('.like-count'); 
            let dislikeButton = $(this);
            let likeButton = $(this).siblings('.like-btn');

            $.ajax({
                url: ajax_comments_rating.ajaxurl,
                type: 'POST',
                data: {
                    action: 'dislike_comment',
                    comment_id: commentID,
                    nonce: ajax_comments_rating.nonce
                },
                success: function (response) {
                    if (response.success) {

                        dislikeCountElement.text(response.data.dislikes_count);
                        likeCountElement.text(response.data.likes_count);

                        if (dislikeButton.hasClass('active')) {
                            dislikeButton.removeClass('active');
                        } else {
                            dislikeButton.addClass('active');
                            likeButton.removeClass('active');
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error in Dislike AJAX request:', xhr.status, xhr.statusText);
                }
            });
        });
    }

    initializeCommentRatings();

    $(document).on('commentsRefreshed', function () {
        initializeCommentRatings();
    });
});
