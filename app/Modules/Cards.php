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

        if (!$post_type) {
            return [];
        }

        $enable_load_more = $flexibleContent['enable_load_more'] ?? false;
        $item_number = $flexibleContent['item_number'];
        $redirect_link = $flexibleContent['redirect_link'];
        $title_and_description = $flexibleContent['title_and_description'];
        $search_form = $flexibleContent['search_form'];
        $posts = [];

        $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $selected_brand = isset($_GET['brand']) ? sanitize_text_field($_GET['brand']) : '';
        $selected_city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';

        if ($post_type === 'Cars') {
            $posts = Car::getFilteredCarsData($selected_brand, $selected_city, $search_query);
        } else if ($post_type === 'Authors') {
            $posts = self::getAuthorsData();
        }

        return [
            'posts' => $posts,
            'enable_load_more' => $enable_load_more,
            'item_number' => $item_number,
            'redirect_link' => $redirect_link,
            'title_and_description' => $title_and_description,
            'search_form' => $search_form,
            'search_query' => $search_query,
            'selected_brand' => $selected_brand,
            'selected_city' => $selected_city,
            'post_type' => $post_type 
        ];
    }

    public static function getAuthorsData() {
        $query = new \WP_Query([
            'post_type' => 'authors',
            'posts_per_page' => -1,
        ]);

        $authors = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $authorID = get_the_ID();
                $authorDetails = get_field('authors', $authorID);

                $authors[] = [
                    'title' => get_the_title($authorID),
                    'author_image' => get_the_post_thumbnail_url($authorID, 'full'),
                    'author_location' => $authorDetails['author_location'],
                    'author_description' => apply_filters('the_content', get_the_content($authorID)),
                    'post_type' => 'authors',
                    'link' => get_permalink($authorID),
                ];
            }
            wp_reset_postdata();
        }

        return $authors;
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
        ]);
    }
}
