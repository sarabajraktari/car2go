<?php

namespace Internship\PostTypes;

use Internship\Includes\Setup;

class TaxonomyData {

    // Function to get taxonomy terms
    public static function getTaxonomyData($taxonomy) {
        // Map your taxonomy names to the correct slugs
        $taxonomyMap = [
            'brand' => 'car_brand',
            'city' => 'car_city',
        ];

        if (!isset($taxonomyMap[$taxonomy])) {
            error_log('Invalid taxonomy key: ' . $taxonomy);
            return [];
        }

        $taxonomySlug = $taxonomyMap[$taxonomy];

        // Fetch terms for the specified taxonomy
        $terms = get_terms([
            'taxonomy' => $taxonomySlug,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            error_log('Error fetching terms: ' . $terms->get_error_message());
            return [];
        }

        // Format the terms into an array
        $formattedTerms = array_map(function ($term) use ($taxonomySlug) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'description' => $term->description,
                'count' => $term->count,
                'link' => get_term_link($term),
                'posts' => self::getPostsByTerm($taxonomySlug, $term->term_id) // Fetch posts for this term
            ];
        }, $terms);

        return $formattedTerms;
    }

    // Function to get posts by a taxonomy term
    private static function getPostsByTerm($taxonomySlug, $termId) {
        $args = [
            'post_type' => 'cars',
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => $taxonomySlug,
                    'field'    => 'term_id',
                    'terms'    => $termId,
                ]
            ]
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $posts = [];
            while ($query->have_posts()) {
                $query->the_post();
                $posts[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'link' => get_permalink(),
                    'excerpt' => get_the_excerpt(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                ];
            }
            wp_reset_postdata();
            return $posts;
        }
        return [];
    }
}
