<?php

namespace Internship\PostTypes;

use Internship\Includes\Setup;

class Car {

    public static function getSingleCarData($carSlug) {
        $args = array(
            'post_type' => 'cars',
            'name' => $carSlug, // Filter by the car slug (used in the URL)
            'posts_per_page' => 1 
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $query->the_post();
            $carID = get_the_ID();
            $carDetails = get_field('car_details', $carID);

            $carData = [
                'title' => get_the_title($carID),
                'description' => apply_filters('the_content', get_the_content($carID)), 
                'car_image' => get_the_post_thumbnail_url($carID, 'full'),
                'car_features' => $carDetails['car_features'] ?? null,
                'iframe' => $carDetails['iframe'] ?? null,
                'specifications' => is_array($carDetails['specifications']) ? array_map(function($spec) {
                    return [
                        'title' => $spec['specifications_title'],
                        'description' => $spec['specifications_description'],
                        'image' => $spec['specifications_image'],
                    ];
                }, $carDetails['specifications']) : [],
            ];

            wp_reset_postdata(); 

            return $carData;
        }

        return [];
    }
    // Fetch multiple cars with optional filtering
    public static function getFilteredCarsData($brand_slug = '', $city_slug = '', $search_query = '') {
        $args = array(
            'post_type' => 'cars',
            'posts_per_page' => -1,
            's' => $search_query,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'car_brand',
                    'field'    => 'slug',
                    'terms'    => $brand_slug,
                    'operator' => $brand_slug ? 'IN' : 'EXISTS',
                ),
                array(
                    'taxonomy' => 'car_city',
                    'field'    => 'slug',
                    'terms'    => $city_slug,
                    'operator' => $city_slug ? 'IN' : 'EXISTS',
                )
            )
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $cars = [];
            while ($query->have_posts()) {
                $query->the_post();
                $carID = get_the_ID();
                $carDetails = get_field('car_details', $carID);

                $cars[] = [
                    'title' => get_the_title($carID),
                    'description' => get_post_field('post_content', get_the_ID($carID)),
                    'car_image' => get_the_post_thumbnail_url($carID, 'full'),
                    'link' => get_permalink($carID),
                    'rent_details' => $carDetails['rent_details'],
                ];
            }
            wp_reset_postdata();
            return $cars;
        }

        return [];
    }
}