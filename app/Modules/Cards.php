<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class Cards implements ModuleInterface {

    public static function getData($key) {
        $modules = get_field('modules_list');

        if (!isset($modules[$key])) {
            return [];
        }
        
        // Get the specific module's data using the key
        $flexibleContent = $modules[$key];
    
        // Fetch the selected post type from the ACF field
        $post_type = $flexibleContent['select_post_types']; 
    
        if (!$post_type) {
            return [];
        }

        $enable_load_more = $flexibleContent['enable_load_more'] ?? false;
        $item_number = $flexibleContent['item_number'];
        $redirect_link = $flexibleContent['redirect_link'];
        $title_and_description = $flexibleContent['title_and_description'];
        

        $posts = [];

        // Check the selected post type and fetch data accordingly
        if ($post_type === 'Cars') {

            $query = new \WP_Query([
                'post_type' => 'cars',
                'posts_per_page' => -1,
            ]);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $carDetails = get_field('car_details', get_the_ID());
                    
                    $posts[] = [
                        'title'       => get_the_title(),
                        'description' => get_post_field('post_content', get_the_ID()),
                        'thumbnail'   => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                        'link'        => get_permalink(), 
                        'rent_details' => $carDetails['rent_details'],
                    ];
                }
                wp_reset_postdata();
            }
        } else if ($post_type === 'Authors') {
            $query = new \WP_Query([
                'post_type' => 'authors',
                'posts_per_page' => -1, 
            ]);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $posts[] = [
                        'title'       => get_the_title(),
                        'description' => get_post_field('post_content', get_the_ID()),
                        'thumbnail'   => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                        'link'        => get_permalink(), 
                    ];
                }
                wp_reset_postdata();
            }
        }
        
        return [
            'posts' => $posts,
            'enable_load_more' => $enable_load_more,
            'item_number' => $item_number,
            'redirect_link' => $redirect_link,
            'title_and_description' => $title_and_description,
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/Cards.twig', [
            'data' => $data['posts'],
            'enable_load_more' => $data['enable_load_more'],
            'item_number' => $data['item_number'],
            'redirect_link' => $data['redirect_link'],
            'title_and_description' => $data['title_and_description'],
        ]);
    }
}
