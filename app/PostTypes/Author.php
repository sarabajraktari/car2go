<?php

namespace Internship\PostTypes;

use Internship\Includes\Setup;

class Author {

    public static function getSingleAuthorData($authorSlug) {
        $args = array(
            'post_type' => 'authors',
            'name' => $authorSlug, // Filter by the author slug (used in the URL)
            'posts_per_page' => 1 
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $query->the_post();
            $authorID = get_the_ID();
            $authorDetails = get_field('authors', $authorID);

            $authorData = [
                'title' => get_the_title($authorID),
                'author_image' => get_the_post_thumbnail_url($authorID, 'full'),
                'author_location' => $authorDetails['author_location'],
                'author_description' => apply_filters('the_content', get_the_content($authorID)),
            ];

            wp_reset_postdata(); 

            return $authorData;
        }
    }
}
