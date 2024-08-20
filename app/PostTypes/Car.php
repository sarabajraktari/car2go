<?php

namespace Internship\PostTypes;

use Internship\Includes\Setup;

class Car {

    public static function getSingleCarData($carSlug) {
        $args = array(
            'post_type' => 'car-post',
            'name' => $carSlug, // Filter by the car slug (used in the URL)
            'posts_per_page' => 1 
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $query->the_post();
            $carID = get_the_ID();
            $carDetails = get_field('car_details', $carID);

            $carData = [
                'title_and_description' => $carDetails['title_and_description'],
                'car_image' => $carDetails['car_image'],
                'car_features' => $carDetails['car_features']
            ];

            wp_reset_postdata(); 

            return $carData;
        }
        
    }
}