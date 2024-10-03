jQuery(document).ready(function ($) {

    function initializeCommentRatings() {

        console.log("Checking initial like/dislike status");
        $.each(ajax_comments_rating.comments_status, function (commentID, status) {
            console.log("Status for comment ID:", commentID, " - Liked:", status.liked, " Disliked:", status.disliked);
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

            console.log("Like button clicked for comment ID:", commentID);

            $.ajax({
                url: ajax_comments_rating.ajaxurl,
                type: 'POST',
                data: {
                    action: 'like_comment',
                    comment_id: commentID,
                    nonce: ajax_comments_rating.nonce
                },
                success: function (response) {
                    console.log("AJAX success for Like:", response);
                    if (response.success) {

                        likeCountElement.text(response.data.likes_count);
                        dislikeCountElement.text(response.data.dislikes_count);

                        if (likeButton.hasClass('active')) {
                            likeButton.removeClass('active');
                        } else {
                            likeButton.addClass('active');
                            dislikeButton.removeClass('active');
                        }
                    } else {
                        console.log("AJAX response was unsuccessful:", response);
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

            console.log("Dislike button clicked for comment ID:", commentID);

            $.ajax({
                url: ajax_comments_rating.ajaxurl,
                type: 'POST',
                data: {
                    action: 'dislike_comment',
                    comment_id: commentID,
                    nonce: ajax_comments_rating.nonce
                },
                success: function (response) {
                    console.log("AJAX success for Dislike:", response);
                    if (response.success) {

                        dislikeCountElement.text(response.data.dislikes_count);
                        likeCountElement.text(response.data.likes_count);


                        if (dislikeButton.hasClass('active')) {
                            dislikeButton.removeClass('active');
                        } else {
                            dislikeButton.addClass('active');
                            likeButton.removeClass('active');
                        }
                    } else {
                        console.log("AJAX response was unsuccessful:", response);
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
