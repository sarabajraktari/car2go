<?php

require_once 'vendor/autoload.php';


function remove_author_permalink($post_link, $post) {
    if ($post->post_type == 'authors') {
        return ''; // Returning an empty string removes the permalink.
    }
    return $post_link;
}
add_filter('post_type_link', 'remove_author_permalink', 10, 2);


//! Register SCSS and JS scripts.
function enqueue_theme_assets() {
    wp_enqueue_style('front_style', get_stylesheet_directory_uri() . '/assets/dist/css/front.css', [], wp_get_theme(get_template())->Version);
    wp_enqueue_script('front_script', get_stylesheet_directory_uri() . '/assets/dist/js/front.js', [], wp_get_theme(get_template())->Version, true);
    wp_enqueue_script('front_admin_script', get_stylesheet_directory_uri() . '/assets/dist/js/admin/app.js', [], wp_get_theme(get_template())->Version, true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');


function enqueue_custom_scripts() {
    // Enqueue your custom JavaScript file
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/modules/header-mobile-menu.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function enqueue_splide_assets() {
    // Enqueue Splide CSS
    wp_enqueue_style('splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.9/dist/css/splide.min.css');
    
    // Enqueue Splide JS
    wp_enqueue_script('splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.9/dist/js/splide.min.js', array(), '3.6.9', true);
    
    // Enqueue your custom JS to initialize Splide
    wp_enqueue_script('custom-splide-js', get_template_directory_uri() . '/assets/js/modules/custom-splide.js', array('splide-js'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_splide_assets');


// Remove the editor from the 'post' post type
function remove_editor_from_post() {
    remove_post_type_support('post', 'editor');
}
add_action('init', 'remove_editor_from_post');

// Remove the editor from the 'page' post type
function remove_editor_from_page() {
    remove_post_type_support('page', 'editor');
}
add_action('init', 'remove_editor_from_page');

function my_theme_setup() {
    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'my_theme_setup');


//! Check if acf field plugin is active
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
    if (is_admin()) {
        echo "<h2 style='text-align: center; color:red;'>Warning: You need to have ACF Pro Activated!</h2>";
    }
}

add_filter('acf/settings/save_json', function($path) {
    $path = get_template_directory() . '/acf-json';
    return $path;
});

add_filter('acf/settings/load_json', function($paths) {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});


add_filter('timber/context', function($context) {
    $context['home_url'] = home_url();
});


// Register navigation menus
add_action('after_setup_theme', function () {
    register_nav_menus([
        'header-menu' => __('Header Menu', 'my-theme'),
    ]);
});

// Add menu to Timber context
add_filter('timber/context', function ($context) {
    $context['menu'] = [
        'items' => Timber::get_menu('header-menu')->items,
    ];
    return $context;
});

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Header Settings',
        'menu_title' => 'Header Settings',
        'menu_slug' => 'header-settings',
        'capability' => 'edit_posts',
        'redirect' => false,
		'position' => 10,  
        'icon_url' => 'dashicons-admin-customizer',
    ));
}

add_filter('timber/context', function($context) {
    $context['header'] = [
        'menu_items' => Timber::get_menu('header-menu')->items,
        'site_name' => get_bloginfo('name'),
        'home_url' => home_url(),
        'logo' => get_field('header_logo', 'option'),
    ];
    return $context;
});
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Footer Settings',
        'menu_title' => 'Footer Settings',
        'menu_slug' => 'footer-settings',
        'capability' => 'edit_posts',
        'redirect' => false,
        'position' => 10,  
        'icon_url' => 'dashicons-admin-customizer',
    ));
}


add_action( 'init', function() {
	register_post_type( 'cars', array(
	'labels' => array(
		'name' => 'Cars',
		'singular_name' => 'Cars',
		'menu_name' => 'Cars',
		'all_items' => 'All Cars',
		'edit_item' => 'Edit Car',
		'view_item' => 'View Car',
		'view_items' => 'View Cars',
		'add_new_item' => 'Add New Car',
		'new_item' => 'New Car',
		'parent_item_colon' => 'Parent Cars:',
		'search_items' => 'Search Cars',
		'not_found' => 'No cars found',
		'not_found_in_trash' => 'No cars found in Trash',
		'archives' => 'Cars Archives',
		'attributes' => 'Cars Attributes',
		'insert_into_item' => 'Insert into cars',
		'uploaded_to_this_item' => 'Uploaded to this cars',
		'filter_items_list' => 'Filter cars list',
		'filter_by_date' => 'Filter cars by date',
		'items_list_navigation' => 'Cars list navigation',
		'items_list' => 'Cars list',
		'item_published' => 'Cars published.',
		'item_published_privately' => 'Cars published privately.',
		'item_reverted_to_draft' => 'Cars reverted to draft.',
		'item_scheduled' => 'Cars scheduled.',
		'item_updated' => 'Cars updated.',
		'item_link' => 'Cars Link',
		'item_link_description' => 'A link to a cars.',
	),
	'public' => true,
	'show_in_rest' => true,
	'menu_position' => 30,
	'menu_icon' => 'dashicons-car',
	'supports' => array(
            0 => 'title',
            1 => 'editor',
            2 => 'thumbnail',
            4 => 'revisions'
        ),
	'delete_with_user' => false,
) );
} );

    // Register the Brand taxonomy
    register_taxonomy( 'car_brand', 'cars', array(
        'labels' => array(
            'name' => 'Brands',
            'singular_name' => 'Brand',
            'menu_name' => 'Car Brands',
            'all_items' => 'All Brands',
            'edit_item' => 'Edit Brand',
            'view_item' => 'View Brand',
            'update_item' => 'Update Brand',
            'add_new_item' => 'Add New Brand',
            'new_item_name' => 'New Brand Name',
            'search_items' => 'Search Brands',
            'popular_items' => 'Popular Brands',
            'separate_items_with_commas' => 'Separate brands with commas',
            'add_or_remove_items' => 'Add or remove brands',
            'choose_from_most_used' => 'Choose from the most used brands',
            'not_found' => 'No brands found',
        ),
        'public' => true,
        'show_in_rest' => true,
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => array( 'slug' => 'brand' ),
    ));

    // Register the Cities taxonomy
    register_taxonomy( 'car_city', 'cars', array(
        'labels' => array(
            'name' => 'Cities',
            'singular_name' => 'City',
            'menu_name' => 'Cities',
            'all_items' => 'All Cities',
            'edit_item' => 'Edit City',
            'view_item' => 'View City',
            'update_item' => 'Update City',
            'add_new_item' => 'Add New City',
            'new_item_name' => 'New City Name',
            'search_items' => 'Search Cities',
            'popular_items' => 'Popular Cities',
            'separate_items_with_commas' => 'Separate cities with commas',
            'add_or_remove_items' => 'Add or remove cities',
            'choose_from_most_used' => 'Choose from the most used cities',
            'not_found' => 'No cities found',
        ),
        'public' => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => array( 'slug' => 'city' ),
    ));


// author post type

add_action( 'init', function() {
	register_post_type( 'authors', array(
	'labels' => array(
		'name' => 'Authors',
		'singular_name' => 'Author',
		'menu_name' => 'Authors',
		'all_items' => 'All Authors',
		'edit_item' => 'Edit Author',
		'view_item' => 'View Author',
		'view_items' => 'View Authors',
		'add_new_item' => 'Add New Author',
		'new_item' => 'New Author',
		'parent_item_colon' => 'Parent Author:',
		'search_items' => 'Search Authors',
		'not_found' => 'No authors found',
		'not_found_in_trash' => 'No authors found in Trash',
		'archives' => 'Author Archives',
		'attributes' => 'Author Attributes',
		'insert_into_item' => 'Insert into author',
		'uploaded_to_this_item' => 'Uploaded to this author',
		'filter_items_list' => 'Filter authors list',
		'filter_by_date' => 'Filter authors by date',
		'items_list_navigation' => 'Authors list navigation',
		'items_list' => 'Authors list',
		'item_published' => 'Author published.',
		'item_published_privately' => 'Author published privately.',
		'item_reverted_to_draft' => 'Author reverted to draft.',
		'item_scheduled' => 'Author scheduled.',
		'item_updated' => 'Author updated.',
		'item_link' => 'Author Link',
		'item_link_description' => 'A link to a author.',
	),
	'public' => true,
	'show_in_rest' => true,
	'menu_position' => 30,
	'menu_icon' => 'dashicons-admin-users',
	'supports' => array(
            0 => 'title',
            1 => 'editor',
            2 => 'thumbnail',
            4 => 'revisions'
        ),
	'delete_with_user' => false,
) );
} );

// functions.php or a plugin file

add_action('wp_ajax_get_car_suggestions', 'get_car_suggestions');
add_action('wp_ajax_nopriv_get_car_suggestions', 'get_car_suggestions');

function get_car_suggestions() {
    $search_query = sanitize_text_field($_GET['query']);

    $args = array(
        'post_type' => 'cars',
        's' => $search_query,
        'posts_per_page' => 5, // in here you can Limit the number of suggestions
    );

    $query = new WP_Query($args);
    $suggestions = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $suggestions[] = [
                'title' => get_the_title(),
                'link' => get_permalink(),
            ];
        }
    }

    wp_reset_postdata();

    echo json_encode($suggestions);
    wp_die();
}
