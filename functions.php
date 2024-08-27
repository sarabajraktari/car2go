<?php

require_once 'vendor/autoload.php';

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
		1 => 'revisions',
	),
	'delete_with_user' => false,
) );
} );
// Register Custom Taxonomy for Cars
add_action( 'init', function() {
	register_taxonomy( 'car', array( 'cars' ), array(
		'labels' => array(
			'name' => 'Cars',
			'singular_name' => 'Car',
			'menu_name' => 'Cars',
			'all_items' => 'All Cars',
			'edit_item' => 'Edit Car',
			'view_item' => 'View Car',
			'update_item' => 'Update Car',
			'add_new_item' => 'Add New Car',
			'new_item_name' => 'New Car Name',
			'search_items' => 'Search Cars',
			'popular_items' => 'Popular Cars',
			'separate_items_with_commas' => 'Separate cars with commas',
			'add_or_remove_items' => 'Add or remove cars',
			'choose_from_most_used' => 'Choose from the most used cars',
			'not_found' => 'No cars found',
			'no_terms' => 'No cars',
			'items_list_navigation' => 'Cars list navigation',
			'items_list' => 'Cars list',
			'back_to_items' => '← Go to cars',
			'item_link' => 'Car Link',
			'item_link_description' => 'A link to a car',
		),
		'public' => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
	) );
} );

// Registered 'Cards' Custom Post Type
add_action('init', function() {
    register_post_type('cards', array(
        'labels' => array(
            'name' => 'Cards',
            'singular_name' => 'Card',
            'menu_name' => 'Cards',
            'all_items' => 'All Cards',
            'edit_item' => 'Edit Card',
            'view_item' => 'View Card',
            'view_items' => 'View Cards',
            'add_new_item' => 'Add New Card',
            'new_item' => 'New Card',
            'parent_item_colon' => 'Parent Card:',
            'search_items' => 'Search Cards',
            'not_found' => 'No cards found',
            'not_found_in_trash' => 'No cards found in Trash',
            'archives' => 'Card Archives',
            'attributes' => 'Card Attributes',
            'insert_into_item' => 'Insert into card',
            'uploaded_to_this_item' => 'Uploaded to this card',
            'filter_items_list' => 'Filter cards list',
            'filter_by_date' => 'Filter cards by date',
            'items_list_navigation' => 'Cards list navigation',
            'items_list' => 'Cards list',
            'item_published' => 'Card published.',
            'item_published_privately' => 'Card published privately.',
            'item_reverted_to_draft' => 'Card reverted to draft.',
            'item_scheduled' => 'Card scheduled.',
            'item_updated' => 'Card updated.',
            'item_link' => 'Card Link',
            'item_link_description' => 'A link to a card.',
        ),
        'public' => true,
        'show_in_rest' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-index-card',
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'delete_with_user' => false,
    ));
});

// Added meta box to select post type in 'Cards' post type
function add_post_type_meta_box() {
    add_meta_box(
        'select_post_type',
        'Select Post Type',
        'render_post_type_meta_box',
        'cards',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_post_type_meta_box');

function render_post_type_meta_box($post) {
    $postTypes = get_post_types(array('public' => true), 'objects');
    $selectedPostType = get_post_meta($post->ID, 'post_type', true);

    echo '<label for="post_type_select">Choose a post type:</label>';
    echo '<select name="post_type_select" id="post_type_select">';
    foreach ($postTypes as $postType) {
        echo '<option value="' . esc_attr($postType->name) . '" ' . selected($selectedPostType, $postType->name, false) . '>' . esc_html($postType->label) . '</option>';
    }
    echo '</select>';
}

// In this section of code we Save the selected post type
function save_post_type_meta_box($post_id) {
    if (array_key_exists('post_type_select', $_POST)) {
        update_post_meta(
            $post_id,
            'post_type',
            $_POST['post_type_select']
        );
    }
}
add_action('save_post', 'save_post_type_meta_box');
