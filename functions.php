<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer's autoloader

// Function to send email using PHPMailer
function send_email($to, $subject, $body)
{
    $mail = new PHPMailer(true); // Create a new PHPMailer instance

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'email';                       // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'email';                   // SMTP username (your Gmail address)
        $mail->Password   = 'password';                     // SMTP password (the app password you generated)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = //port;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('email', 'Car2Go');             // Set the sender's email and name
        $mail->addAddress($to);                                     // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = $subject;                                  // Email subject
        $mail->Body    = $body;                                     // Email body

        // Send the email
        $mail->send();
        return true; // Return true on success
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"); // Log the error
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; // Return error message
    }
}

// Function to notify subscribers on car price update
function notify_subscribers_on_car_update($post_id, $post, $update)
{
    // Prevent infinite loops or accidental triggering
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Only proceed if the post status is 'publish'
    if ($post->post_status != 'publish') {
        return;
    }

    // Avoid running the code when the hook is triggered by a quick edit or bulk action
    if (wp_is_post_revision($post_id)) {
        return;
    }

    $car_name = $post->post_title;
    $car_image = get_the_post_thumbnail_url($post_id); // Get car image

    // Define the meta key path for rent_details->price
    $meta_key_price = 'car_details_rent_details_price';

    if (!$update) {
        // New post logic

        // Set email content for new car
        $subject = 'New Car Added: ' . $car_name;
        $message = "
            <h1>New Car Added!</h1>
            <p>A new car, <strong>$car_name</strong>, has been added to our inventory!</p>
           
            <p>Check it out on our website!</p>
        ";

        // Retrieve users who have subscribed for updates
        $subscribed_users = get_users([
            'meta_key' => 'subscribe_newsletter',
            'meta_value' => 1,
        ]);

        // Send email to each subscribed user
        foreach ($subscribed_users as $user) {
            send_email($user->user_email, $subject, $message); // Use PHPMailer to send the email
        }
    } else {
        // Update post logic

        // Get previous price (before the update)
        $previous_price = get_post_meta($post_id, $meta_key_price, true);
        $current_price = isset($_POST['car_details']['rent_details']['price']) ? $_POST['car_details']['rent_details']['price'] : '';

        // Check if the price has been updated
        if ($previous_price && $previous_price != $current_price) {
            // Update the meta field with the new price
            update_post_meta($post_id, $meta_key_price, $current_price);

            // Set email content for price update
            $subject = 'Price Update for: ' . $car_name;
            $message = "
                <h1>Price Updated!</h1>
                <p>The price for <strong>$car_name</strong> has been updated!</p>
             
                <p>Check out the new price details on our website!</p>
            ";

            // Retrieve users who have subscribed for updates
            $subscribed_users = get_users([
                'meta_key' => 'subscribe_newsletter',
                'meta_value' => 1,
            ]);

            // Send email to each subscribed user
            foreach ($subscribed_users as $user) {
                send_email($user->user_email, $subject, $message); // Use PHPMailer to send the email
            }
        }
    }
}

// Hook to notify subscribers when a new car is added or updated
add_action('wp_insert_post', 'notify_subscribers_on_car_update', 10, 3);




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
    wp_enqueue_script('sidebar_script', get_stylesheet_directory_uri() . '/assets/js/modules/SideBar.js', [], wp_get_theme(get_template())->Version, true);
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


function enqueue_comment_ajax_script() {
    wp_enqueue_script('ajax-comment-script', get_template_directory_uri() . '/assets/js/modules/comments-section.js', array('jquery'), null, true);
   
    wp_localize_script('ajax-comment-script', 'ajax_comments', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('submit_comment_nonce') 
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_comment_ajax_script');


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


// Register navigation menus -> edited this part over here
add_action('after_setup_theme', function () {
    register_nav_menus([
        'header-menu' => __('Header Menu', 'my-theme'),
    ]);
});


add_filter('wp_insert_post_data', function($data, $postarr) {
    if ($data['post_type'] == 'cars' && empty($postarr['ID'])) {
        // Enable comments by default for new posts of type 'cars'
        $data['comment_status'] = 'open';
    }
    return $data;
}, 10, 2);


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
            3 => 'comments',
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


add_action('wp_ajax_get_car_suggestions', 'get_car_suggestions');
add_action('wp_ajax_nopriv_get_car_suggestions', 'get_car_suggestions');


function get_car_suggestions() {
    global $wpdb;


    $search_query = sanitize_text_field($_GET['query']);
    $selected_brand = isset($_GET['brand_slug']) ? sanitize_text_field($_GET['brand_slug']) : '';
    $selected_city = isset($_GET['city_slug']) ? sanitize_text_field($_GET['city_slug']) : '';
   
    $search_query_normalized = str_replace(['-', ' '], ' ', $search_query);


    $args = array(
        'post_type' => 'cars',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        's' => '',
    );


    $tax_query = array('relation' => 'AND');
   
    if (!empty($selected_brand)) {
        $tax_query[] = array(
            'taxonomy' => 'car_brand',
            'field'    => 'slug',
            'terms'    => $selected_brand,
        );
    }


    if (!empty($selected_city)) {
        $tax_query[] = array(
            'taxonomy' => 'car_city',
            'field'    => 'slug',
            'terms'    => $selected_city,
        );
    }


    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }


    $exact_match_posts = $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT ID, post_title
            FROM $wpdb->posts
            WHERE post_title REGEXP %s
            AND post_type = 'cars'
            AND post_status = 'publish'
            ",
            '(^|\s)' . $wpdb->esc_like($search_query_normalized)
        )
    );


    $suggestions = [];


    if ($exact_match_posts) {
        foreach ($exact_match_posts as $post) {


            if (!empty($selected_brand)) {
                $post_id = $post->ID;
                $post_brands = wp_get_post_terms($post_id, 'car_brand', array('fields' => 'slugs'));
                if (!in_array($selected_brand, $post_brands)) {
                    continue;
                }
            }
            if (!empty($selected_city)) {
                $post_id = $post->ID;
                $post_cities = wp_get_post_terms($post_id, 'car_city', array('fields' => 'slugs'));
                if (!in_array($selected_city, $post_cities)) {
                    continue;
                }
            }


            $suggestions[] = [
                'title' => $post->post_title,
                'link' => get_permalink($post->ID),
            ];
        }
    }


    wp_reset_postdata();
   
    echo json_encode($suggestions);
    wp_die();
}


add_action( 'init', function() {
    register_post_type( 'rent_now', array(
        'labels' => array(
            'name' => 'Rent Now',
            'singular_name' => 'Rent Now',
            'menu_name' => 'Rent Now',
            'all_items' => 'All Rent Now Posts',
            'edit_item' => 'Edit Rent Now',
            'view_item' => 'View Rent Now',
            'view_items' => 'View Rent Now',
            'add_new_item' => 'Add New Rent Now',
            'new_item' => 'New Rent Now',
            'parent_item_colon' => 'Parent Rent Now:',
            'search_items' => 'Search Rent Now',
            'not_found' => 'No rent now posts found',
            'not_found_in_trash' => 'No rent now posts found in Trash',
            'archives' => 'Rent Now Archives',
            'attributes' => 'Rent Now Attributes',
            'insert_into_item' => 'Insert into Rent Now',
            'uploaded_to_this_item' => 'Uploaded to this Rent Now',
            'filter_items_list' => 'Filter Rent Now list',
            'filter_by_date' => 'Filter Rent Now by date',
            'items_list_navigation' => 'Rent Now list navigation',
            'items_list' => 'Rent Now list',
            'item_published' => 'Rent Now post published.',
            'item_published_privately' => 'Rent Now post published privately.',
            'item_reverted_to_draft' => 'Rent Now post reverted to draft.',
            'item_scheduled' => 'Rent Now post scheduled.',
            'item_updated' => 'Rent Now post updated.',
            'item_link' => 'Rent Now Link',
            'item_link_description' => 'A link to a Rent Now post.',
        ),
        'public' => true,
        'show_in_rest' => true,
        'menu_position' => 31,
        'menu_icon' => 'dashicons-admin-network',
        'supports' => array(
            'title',
            'revisions',
        ),
        'delete_with_user' => false,
    ));
});


function create_rent_now_post_when_car_published( $post_id ) {
    if ( get_post_type( $post_id ) == 'cars' && get_post_status( $post_id ) == 'publish' ) {
       
        // Check if a related Rent Now post already exists
        $existing_rent_now = new WP_Query(array(
            'post_type' => 'rent_now',
            'meta_key' => 'related_car',
            'meta_value' => $post_id,
        ));


        if ($existing_rent_now->have_posts()) {
            return; // Rent Now post already exists, no need to create another
        }


        $car_title = get_the_title( $post_id );
        $car_slug = sanitize_title( $car_title );


        $rent_now_post = array(
            'post_title'    => $car_title, // Use the car's title directly, without prefix
            'post_content'  => 'Rent this car now!',
            'post_status'   => 'publish',
            'post_type'     => 'rent_now',
            'post_name'     => $car_slug, // Explicitly set the post slug to match the car's slug
        );


        // Insert the new post into the database
        $rent_now_post_id = wp_insert_post( $rent_now_post );


        // Link the "rent now" post to the related car using ACF
        update_field( 'related_car', $post_id, $rent_now_post_id );
    }
}
add_action( 'save_post', 'create_rent_now_post_when_car_published' );

function handle_submit_comment() {
    error_log('Handling comment submission via AJAX...');
    
    if (!wp_verify_nonce($_POST['nonce'], 'submit_comment_nonce')) {
        error_log('Invalid nonce.');
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    $current_user = wp_get_current_user();

    $comment_data = [
        'comment_post_ID' => absint($_POST['comment_post_ID']),
        'comment_content' => sanitize_text_field($_POST['comment']),
        'comment_author' => $current_user->display_name,
        'comment_author_email' => $current_user->user_email,
        'user_id' => $current_user->ID,
        'comment_parent' => isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0
    ];

    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        $comment = get_comment($comment_id);
        $comment->avatar = get_avatar($comment->comment_author_email, 64);
        $comment->is_approved = ($comment->comment_approved == '1');
        
        ob_start();
        ?>
        <li class="p-4 bg-white border border-gray-200 rounded-lg shadow-md">
            <div class="flex space-x-4">
                <div class="flex-shrink-0">
                    <?= $comment->avatar; ?>
                </div>
                <div>
                    <div class="font-bold"><?= esc_html($comment->comment_author); ?></div>
                    <div class="text-sm text-gray-600"><?= esc_html($comment->comment_date); ?></div>
                    <?php if (!$comment->is_approved) : ?>
                        <div class="text-sm text-red-500"><em>Your comment is awaiting approval.</em></div>
                    <?php endif; ?>
                    <div class="mt-2 text-gray-800"><?= esc_html($comment->comment_content); ?></div>
                </div>
            </div>
        </li>
        <?php
        $comment_html = ob_get_clean();

        wp_send_json_success(['comment' => $comment_html]);
    } else {
        error_log('Comment insertion failed.');
        wp_send_json_error(['message' => 'Comment submission failed']);
    }
}


add_action('wp_ajax_submit_comment', 'handle_submit_comment');
add_action('wp_ajax_nopriv_submit_comment', 'handle_submit_comment');

add_filter('nonce_life', function() {
    return 24 * 60 * 60; // 24 hours
});

function enable_comments_for_existing_cars_posts() {

    $cars_posts = get_posts([
        'post_type' => 'cars',
        'post_status' => 'publish',
        'posts_per_page' => -1, 
    ]);


    foreach ($cars_posts as $post) {
        if ($post->comment_status != 'open') {
            wp_update_post([
                'ID' => $post->ID,
                'comment_status' => 'open',
            ]);
        }
    }
}

add_action('admin_init', 'enable_comments_for_existing_cars_posts');



