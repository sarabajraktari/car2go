<?php
namespace Internship\PostTypes;

class RentNow {

    // Get single Rent Now post data, including related Car
    public static function getSingleRentNowData($rentNowSlug) {
        $args = array(
            'post_type' => 'rent_now',
            'name' => $rentNowSlug, 
            'posts_per_page' => 1
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $query->the_post();
            $rentNowID = get_the_ID();

            // Get related car (Post Object field in ACF)
            $relatedCar = get_field('related_car', $rentNowID);

            // Check if relatedCar is an array and has at least one element
            $relatedCarData = [];
            
            if (is_array($relatedCar) && !empty($relatedCar[0])) {
                $carID = $relatedCar[0]->ID; 

                $carDetails = get_field('car_details', $carID); 

                
                $relatedCarData = [
                    'title' => get_the_title($carID),
                    'car_image' => get_the_post_thumbnail_url($carID, 'full'),
                    'description' => apply_filters('the_content', get_the_content($carID)),
                    'car_features' => $carDetails['car_features'] ?? null,
                    'specifications' => is_array($carDetails['specifications']) ? array_map(function($spec) {
                        return [
                            'title' => $spec['specifications_title'],
                            'description' => $spec['specifications_description'],
                            'image' => $spec['specifications_image'],
                        ];
                    }, $carDetails['specifications']) : [],
                    'rent_details' => $carDetails['rent_details'] ?? null,  
                    'iframe' => $carDetails['iframe'] ?? null,
                    'car_link' => get_permalink($carID),
                ];
            }

     
            $rentNowData = [
                'title' => get_the_title($rentNowID),
                'description' => apply_filters('the_content', get_the_content($rentNowID)),
                'rent_now_image' => get_the_post_thumbnail_url($rentNowID, 'full'),
                'related_car' => $relatedCarData,
            ];

            wp_reset_postdata();

            return $rentNowData;
        }

        return [];
    }
         // Get the Rent Now URL based on the related car
    public static function getRentNowUrlForCar($carID) {
        $rentNowPosts = get_posts([
            'post_type' => 'rent_now',
            'meta_query' => [
                [
                    'key' => 'related_car',
                    'value' => $carID,
                    'compare' => 'LIKE'
                ]
            ],
            'posts_per_page' => 1,
        ]);

        if (!empty($rentNowPosts)) {
            return get_permalink($rentNowPosts[0]->ID); 
        }

        return null; 
    }
}
