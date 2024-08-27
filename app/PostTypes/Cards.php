<?php

namespace Internship\PostTypes;

class Cards {

    public static function getCardData($postType) {
        $args = array(
            'post_type' => $postType,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $query = new \WP_Query($args);
        $cardsData = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                // Fetch the custom image from ACF
                $customImage = get_field('car_image'); // -> Replace '('image')' with  ACF field name
                
                // Fallback to the post thumbnail if no custom image is available
                $imageUrl = $customImage ? $customImage['url'] : get_the_post_thumbnail_url(get_the_ID(), 'full');

                $cardsData[] = [
                    'title' => get_the_title(),
                    'excerpt' => get_the_excerpt(),
                    'link' => get_permalink(),
                    'image' => $imageUrl, // Use the custom image if it exists, otherwise use the thumbnail
                ];
            }
            wp_reset_postdata();
        }

        return $cardsData;
    }
}
