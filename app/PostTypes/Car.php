<?php

namespace Internship\PostTypes;

class Car {

    public static function getFilteredCarsData($brand_slug = '', $city_slug = '', $search_query = '') {
        global $wpdb;
    
        $search_query_normalized = str_replace(['-', ' '], ' ', $search_query);
    
        $args = array(
            'post_type' => 'cars',
            'posts_per_page' => -1,
            's' => $search_query_normalized 
        );
    
        $tax_query = array('relation' => 'AND');
    
        if (!empty($brand_slug)) {
            $tax_query[] = array(
                'taxonomy' => 'car_brand',
                'field'    => 'slug',
                'terms'    => $brand_slug,
                'operator' => 'IN',
            );
        }
    
        if (!empty($city_slug)) {
            $tax_query[] = array(
                'taxonomy' => 'car_city',
                'field'    => 'slug',
                'terms'    => $city_slug,
                'operator' => 'IN',
            );
        }
    
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
    
        $exact_match_posts = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type = 'cars'
                AND post_status = 'publish'
                ",
                $search_query 
            )
        );
    
        if (!empty($exact_match_posts)) {
            $exact_ids = wp_list_pluck($exact_match_posts, 'ID');
            $args['post__in'] = $exact_ids; 
        }
    
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
