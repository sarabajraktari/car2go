<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;
use Internship\PostTypes\Car;
use Internship\PostTypes\Author;

class Cards implements ModuleInterface {

    public static function getData($key) {
        $modules = get_field('modules_list');

        if (!isset($modules[$key])) {
            return [];
        }

        $flexibleContent = $modules[$key];

        $post_type = $flexibleContent['select_post_types'];
        error_log(print_r($post_type, true));

        if (!$post_type) {
            return [];
        }

        $enable_load_more = $flexibleContent['enable_load_more'] ?? false;
        $item_number = $flexibleContent['item_number'];
        $redirect_link = $flexibleContent['redirect_link'];
        $title_and_description = $flexibleContent['title_and_description'];
        $search_form = $flexibleContent['search_form'];
        $posts = [];
        $enable_pagination = $flexibleContent['enable_pagination'] ?? false;

        // Handle search and filter queries
        $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $selected_brand = isset($_GET['brand']) ? sanitize_text_field($_GET['brand']) : '';
        $selected_city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';

        // Pagination logic
        $paged = get_query_var('paged') ? get_query_var('paged') : 1; // Get current page
        $posts_data = self::getPaginationData($post_type, $paged, $item_number, $search_query, $selected_brand, $selected_city);

        return [
            'posts' => $posts_data['posts'], // Current posts for the page
            'enable_load_more' => $enable_load_more,
            'item_number' => $item_number,
            'redirect_link' => $redirect_link,
            'title_and_description' => $title_and_description,
            'search_form' => $search_form,
            'search_query' => $search_query,
            'selected_brand' => $selected_brand,
            'selected_city' => $selected_city,
            'post_type' => $post_type,
            'enable_pagination' => $enable_pagination,
            'max_num_pages' => $posts_data['max_num_pages'], // Total pages for pagination
            'current_page' => $paged // Current page number
        ];
    }

    public static function getPaginationData($post_type, $paged = 1, $posts_per_page = 10, $search_query = '', $selected_brand = '', $selected_city = '') {
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page, // Use the passed posts_per_page value
            'paged' => $paged, // Control pagination using the current page number
        ];

        // Add search and filter conditions to the query if applicable
        if ($search_query) {
            $args['s'] = $search_query; // Search query
        }
        // You can add more filters based on selected_brand or selected_city if needed.

        $query = new \WP_Query($args);

        $posts = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $postID = get_the_ID();

                $posts[] = [
                    'title' => get_the_title($postID),
                    'description' => get_post_field('post_content', $postID),
                    'thumbnail' => get_the_post_thumbnail_url($postID, 'full'),
                    'link' => get_permalink($postID),
                    'post_type' => $post_type,
                ];
            }
            wp_reset_postdata();
        }

        return [
            'posts' => $posts,
            'max_num_pages' => $query->max_num_pages, // Total number of pages
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/Cards.twig', [
            'data' => $data['posts'],
            'enable_load_more' => $data['enable_load_more'],
            'item_number' => $data['item_number'],
            'redirect_link' => $data['redirect_link'],
            'title_and_description' => $data['title_and_description'],
            'search_form' => $data['search_form'],
            'search_query' => $data['search_query'],
            'selected_brand' => $data['selected_brand'],
            'selected_city' => $data['selected_city'],
            'post_type' => $data['post_type'],
            'enable_pagination' => $data['enable_pagination'],
            'max_num_pages' => $data['max_num_pages'], // Add max_num_pages to the data
            'current_page' => $data['current_page'], // Add current_page to the data
        ]);
    }
}
