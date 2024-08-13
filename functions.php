<?php

require_once 'vendor/autoload.php';

//! Register SCSS and JS scripts.
function enqueue_theme_assets() {
    
    wp_enqueue_style('front_style', get_stylesheet_directory_uri() . '/assets/dist/css/front.css', [], wp_get_theme(get_template())->Version);
    wp_enqueue_script('front_script', get_stylesheet_directory_uri() . '/assets/dist/js/front.js', [], wp_get_theme(get_template())->Version, true);
    wp_enqueue_script('front_admin_script', get_stylesheet_directory_uri() . '/assets/dist/js/admin/app.js', [], wp_get_theme(get_template())->Version, true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');

function awesome_theme_setup() {
    add_theme_support('menus');
    register_nav_menu('primary','car2go');
}
add_action('after_setup_theme', 'awesome_theme_setup');

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

function create_authors_post_type() {
    $labels = array(
        'name'               => 'Authors',
        'singular_name'      => 'Author',
        'menu_name'          => 'Authors',
        'name_admin_bar'     => 'Author',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Author',
        'new_item'           => 'New Author',
        'edit_item'          => 'Edit Author',
        'view_item'          => 'View Author',
        'all_items'          => 'All Authors',
        'search_items'       => 'Search Authors',
        'parent_item_colon'  => 'Parent Authors:',
        'not_found'          => 'No authors found.',
        'not_found_in_trash' => 'No authors found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'author'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
    );

    register_post_type( 'author', $args );
}
add_action( 'init', 'create_authors_post_type' );

