<?php

namespace Internship\PostTypes;
use Internship\PostTypes\RentNow;

class Car {

    public static function getFieldValue($field, $default = null) {
        return !empty($field) ? $field : $default;
    }

    public static function getSingleCarData($carSlug) {
        $args = array(
            'post_type' => 'cars',
            'name' => $carSlug,
            'posts_per_page' => 1
        );

        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            return [];
        }

        $query->the_post();
        $carID = get_the_ID();
        $carDetails = get_field('car_details', $carID);

        $carData = [
            'title' => get_the_title($carID),
            'description' => apply_filters('the_content', get_the_content($carID)),
            'car_image' => get_the_post_thumbnail_url($carID, 'full'),
            'car_features' => self::getFieldValue($carDetails['car_features']),
            'specifications' => is_array($carDetails['specifications']) ? array_map(function ($spec) {
                return [
                    'title' => self::getFieldValue($spec['specifications_title']),
                    'description' => self::getFieldValue($spec['specifications_description']),
                    'image' => self::getFieldValue($spec['specifications_image']),
                ];
            }, $carDetails['specifications']) : [],
            'iframe' => self::getFieldValue($carDetails['iframe']),

            'latitude' => $carDetails['car_location_lat'] ?? null,
            'longitude' => $carDetails['car_location_lng'] ?? null,
        
        ];

        wp_reset_postdata();
        return $carData;
    }

    public static function getFilteredCarsData($brand_slug = '', $city_slug = '', $search_query = '') {
        global $wpdb;
    
        // Normalize the search query by replacing hyphens with spaces
        $search_query_normalized = str_replace('-', ' ', $search_query);
    
        $args = [
            'post_type' => 'cars',
            'posts_per_page' => -1,
        ];
    
        // Taxonomy query
        $tax_query = ['relation' => 'AND'];
    
        if (!empty($brand_slug)) {
            $tax_query[] = [
                'taxonomy' => 'car_brand',
                'field'    => 'slug',
                'terms'    => $brand_slug,
                'operator' => 'IN',
            ];
        }
    
        if (!empty($city_slug)) {
            $tax_query[] = [
                'taxonomy' => 'car_city',
                'field'    => 'slug',
                'terms'    => $city_slug,
                'operator' => 'IN',
            ];
        }
    
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }
    
        // Perform search by normalizing both post titles and the search query (replacing hyphens with spaces)
        $exact_match_posts = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE REPLACE(post_title, '-', ' ') LIKE %s
                AND post_type = 'cars'
                AND post_status = 'publish'
                ",
                '%' . $wpdb->esc_like($search_query_normalized) . '%'
            )
        );
    
        if (empty($exact_match_posts)) {
            return [];
        }
    
        // Collect exact IDs from the query results
        $exact_ids = wp_list_pluck($exact_match_posts, 'ID');
        $args['post__in'] = $exact_ids;
    
        // Fetch cars matching the exact titles and taxonomies
        $query = new \WP_Query($args);
    
        if (!$query->have_posts()) {
            return [];
        }
    
        $cars = [];
        while ($query->have_posts()) {
            $query->the_post();
            $carID = get_the_ID();
            $carDetails = get_field('car_details', $carID);

            $rentNowUrl = RentNow::getRentNowUrlForCar($carID);

            $cars[] = [
                'title' => get_the_title($carID),
                'description' => get_post_field('post_content', $carID),
                'thumbnail'   => get_the_post_thumbnail_url($carID, 'full'),
                'car_image' => get_the_post_thumbnail_url($carID, 'full'),
                'link' => get_permalink(),
                'rent_now_url' => $rentNowUrl,
                'rent_details' => self::getFieldValue($carDetails['rent_details']),
                'post_type'   => get_post_type(),
            ];

            
        }
        wp_reset_postdata();

        return $cars;
    }
    
    
}
