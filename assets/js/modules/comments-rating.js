jQuery(document).ready(function ($) {

    function initializeCommentRatings() {

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
                    } else {
                    }
                },
                error: function (xhr, status, error) {
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
                    } else {
                    }
                },
                error: function (xhr, status, error) {
                }
            });
        });
    }


    initializeCommentRatings();

    $(document).on('commentsRefreshed', function () {
        initializeCommentRatings();
    });
});
