<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 


add_action('wp_logout', function() {
    wp_redirect(home_url());  // Redirect to homepage after logging out
    exit();
});


function send_email($to, $subject, $body)
{
    $mail = new PHPMailer(true); 

    try {
    
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                      
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'car2goks@gmail.com';                   
        $mail->Password   = 'kdrpfecfwayninzo';                     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
        $mail->Port       = 587;                                    

        $mail->setFrom('car2goks@gmail.com', 'Car2Go');            
        $mail->addAddress($to);                                     

       
        $mail->isHTML(true);                                      
        $mail->Subject = $subject;                                  
        $mail->Body    = $body;                                     

       
        $mail->send();
        return true; 
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; 
    }
}

function notify_subscribers_on_car_update($post_id, $post, $update)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if ($post->post_status != 'publish') {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    $car_name = $post->post_title;
    $car_image = get_the_post_thumbnail_url($post_id, 'full'); 
    error_log("Car Image URL: $car_image"); 

    $car_link = get_post_type($post_id) === 'cars' ? get_permalink($post_id) : '';

    $logo_url = 'https://i.imgur.com/khWMeKf.png'; 


    if (!$update) {
  
        $subject = 'New Car Added: ' . $car_name;

        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eaeaea; border-radius: 10px;'>
            <div style='text-align: center;'>
                <img src='$logo_url' alt='Car2Go Logo' style='width: auto; height: 70px; margin-bottom: 20px;' />
            </div>
            <div style='background-color: #f4f4f4; padding: 10px; text-align: center;'>
                <h1 style='margin: 0; color: #333;'>New Car Added!</h1>
            </div>
            <div style='padding: 20px;'>
                <p style='font-size: 18px; color: #333;'>Hello,</p>
                <p style='font-size: 16px; color: #555;'>We are excited to inform you that a new car, <strong>$car_name</strong>, has been added to our inventory.</p>
                <div style='text-align: center; margin: 20px 0;'>
                </div>
                <p style='font-size: 16px; color: #555;'>Explore this car and more on our website.</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='$car_link' style='background-color: #007BFF; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 16px;'>View Car</a>
                </div>
            </div>
            <div style='background-color: #f4f4f4; padding: 10px; text-align: center;'>
                <p style='margin: 0; font-size: 14px; color: #888;'>Thank you for choosing our service!</p>
                <p style='margin: 0; font-size: 14px; color: #888;'>Car2Go Team</p>
            </div>
        </div>
    ";

        $subscribed_users = get_users([
            'meta_key' => 'subscribe_newsletter',
            'meta_value' => 1,
        ]);

        foreach ($subscribed_users as $user) {
            send_email($user->user_email, $subject, $message); 
        }
    } else {
        
        $subject = 'Price Update for: ' . $car_name;
        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eaeaea; border-radius: 10px;'>
                <div style='text-align: center;'>
                    <img src='$logo_url' alt='Car2Go Logo' style='width: auto; height: 70px; margin-bottom: 20px;' />
                </div>
                <div style='background-color: #f4f4f4; padding: 10px; text-align: center;'>
                    <h1 style='margin: 0; color: #333;'>Price Updated!</h1>
                </div>
                <div style='padding: 20px;'>
                    <p style='font-size: 18px; color: #333;'>Hello,</p>
                    <p style='font-size: 16px; color: #555;'>The price for <strong>$car_name</strong> has been updated.</p>
                      <div style='text-align: center; margin: 20px 0;'>
                </div>
                    <p style='font-size: 16px; color: #555;'>Check out the details on our website.</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='$car_link' style='background-color: #007BFF; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 16px;'>View Car</a>
                    </div>
                </div>
                <div style='background-color: #f4f4f4; padding: 10px; text-align: center;'>
                    <p style='margin: 0; font-size: 14px; color: #888;'>Thank you for choosing our service!</p>
                    <p style='margin: 0; font-size: 14px; color: #888;'>Car2Go Team</p>
                </div>
            </div>
            ";

        $subscribed_users = get_users([
            'meta_key' => 'subscribe_newsletter',
            'meta_value' => 1,
        ]);

        foreach ($subscribed_users as $user) {
            send_email($user->user_email, $subject, $message); 
        }
    }
}

add_action('wp_insert_post', 'notify_subscribers_on_car_update', 10, 3);





function remove_author_permalink($post_link, $post)
{
    if ($post->post_type == 'authors') {
        return ''; // Returning an empty string removes the permalink.
    }
    return $post_link;
}
add_filter('post_type_link', 'remove_author_permalink', 10, 2);



//! Register SCSS and JS scripts.
function enqueue_theme_assets()
{
    wp_enqueue_style('front_style', get_stylesheet_directory_uri() . '/assets/dist/css/front.css', [], wp_get_theme(get_template())->Version);
    wp_enqueue_script('front_script', get_stylesheet_directory_uri() . '/assets/dist/js/front.js', [], wp_get_theme(get_template())->Version, true);
    wp_enqueue_script('front_admin_script', get_stylesheet_directory_uri() . '/assets/dist/js/admin/app.js', [], wp_get_theme(get_template())->Version, true);
    wp_enqueue_script('sidebar_script', get_stylesheet_directory_uri() . '/assets/js/modules/SideBar.js', [], wp_get_theme(get_template())->Version, true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');



function enqueue_custom_scripts()
{
    // Enqueue your custom JavaScript file
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/modules/header-mobile-menu.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


function enqueue_splide_assets()
{
    // Enqueue Splide CSS
    wp_enqueue_style('splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.9/dist/css/splide.min.css');

    // Enqueue Splide JS
    wp_enqueue_script('splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.9/dist/js/splide.min.js', array(), '3.6.9', true);

    // Enqueue your custom JS to initialize Splide
    wp_enqueue_script('custom-splide-js', get_template_directory_uri() . '/assets/js/modules/custom-splide.js', array('splide-js'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_splide_assets');

function enqueue_comment_scripts() {
    // Enqueue the comment rating JavaScript file
    wp_enqueue_script('comments-rating', get_template_directory_uri() . '/assets/js/modules/comments-rating.js', array('jquery'), null, true);

    // Get the current user ID
    $current_user_id = get_current_user_id();

    // Get all comments and check which ones are liked or disliked by the current user
    $comments_liked_disliked = [];

    $comments = get_comments(); // This will fetch all comments. You can modify this query to optimize it if necessary.

    foreach ($comments as $comment) {
        $liked_by = get_comment_meta($comment->comment_ID, 'liked_by', true) ?: [];
        $disliked_by = get_comment_meta($comment->comment_ID, 'disliked_by', true) ?: [];

        $comments_liked_disliked[$comment->comment_ID] = [
            'liked' => in_array($current_user_id, $liked_by),
            'disliked' => in_array($current_user_id, $disliked_by)
        ];
    }

    // Pass the AJAX URL, nonce, and liked/disliked status to JavaScript
    wp_localize_script('comments-rating', 'ajax_comments_rating', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('ajax_comment_rating_nonce'),
        'comments_status' => $comments_liked_disliked, // Pass the liked/disliked status to the frontend
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_comment_scripts');





function enqueue_comment_ajax_script()
{
    wp_enqueue_script('ajax-comment-script', get_template_directory_uri() . '/assets/js/modules/comments-section.js', array('jquery'), null, true);

    wp_localize_script('ajax-comment-script', 'ajax_comments', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('submit_comment_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_comment_ajax_script');


// Remove the editor from the 'post' post type
function remove_editor_from_post()
{
    remove_post_type_support('post', 'editor');
}
add_action('init', 'remove_editor_from_post');


// Remove the editor from the 'page' post type
function remove_editor_from_page()
{
    remove_post_type_support('page', 'editor');
}
add_action('init', 'remove_editor_from_page');


function my_theme_setup()
{
    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'my_theme_setup');

//************************** */
//Comment Form SPAM Protection
//************************** */

function enqueue_recaptcha_script() {
    echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}
add_action('wp_head', 'enqueue_recaptcha_script');


//************************** */
//Comment Form SPAM Protection
//************************** */


//! Check if acf field plugin is active
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
    if (is_admin()) {
        echo "<h2 style='text-align: center; color:red;'>Warning: You need to have ACF Pro Activated!</h2>";
    }
}


add_filter('acf/settings/save_json', function ($path) {
    $path = get_template_directory() . '/acf-json';
    return $path;
});


add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});



add_filter('timber/context', function ($context) {
    $context['home_url'] = home_url();
});


// Register navigation menus -> edited this part over here
add_action('after_setup_theme', function () {
    register_nav_menus([
        'header-menu' => __('Header Menu', 'my-theme'),
    ]);
});


add_filter('wp_insert_post_data', function ($data, $postarr) {
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


add_filter('timber/context', function ($context) {
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


add_action('init', function () {
    register_post_type('cars', array(
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
    ));
});


// Register the Brand taxonomy
register_taxonomy('car_brand', 'cars', array(
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
    'rewrite' => array('slug' => 'brand'),
));


// Register the Cities taxonomy
register_taxonomy('car_city', 'cars', array(
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
    'rewrite' => array('slug' => 'city'),
));




// author post type


add_action('init', function () {
    register_post_type('authors', array(
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
    ));
});


add_action('wp_ajax_get_car_suggestions', 'get_car_suggestions');
add_action('wp_ajax_nopriv_get_car_suggestions', 'get_car_suggestions');


function get_car_suggestions()
{
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


add_action('init', function () {
    register_post_type('rent_now', array(
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


function create_rent_now_post_when_car_published($post_id)
{
    if (get_post_type($post_id) == 'cars' && get_post_status($post_id) == 'publish') {

        // Check if a related Rent Now post already exists
        $existing_rent_now = new WP_Query(array(
            'post_type' => 'rent_now',
            'meta_key' => 'related_car',
            'meta_value' => $post_id,
        ));


        if ($existing_rent_now->have_posts()) {
            return; // Rent Now post already exists, no need to create another
        }


        $car_title = get_the_title($post_id);
        $car_slug = sanitize_title($car_title);


        $rent_now_post = array(
            'post_title'    => $car_title, // Use the car's title directly, without prefix
            'post_content'  => 'Rent this car now!',
            'post_status'   => 'publish',
            'post_type'     => 'rent_now',
            'post_name'     => $car_slug, // Explicitly set the post slug to match the car's slug
        );


        // Insert the new post into the database
        $rent_now_post_id = wp_insert_post($rent_now_post);


        // Link the "rent now" post to the related car using ACF
        update_field('related_car', $post_id, $rent_now_post_id);
    }
}
add_action('save_post', 'create_rent_now_post_when_car_published');

function handle_submit_comment()
{
    error_log('Handling comment submission via AJAX...');

    // Verify the nonce

    if (!wp_verify_nonce($_POST['nonce'], 'submit_comment_nonce')) {
        error_log('Invalid nonce.');
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    // reCAPTCHA validation
    $recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
    $recaptcha_secret = '6LeB-lYqAAAAAALupC-DduEKg75NZT68o7IGuQ7J'; // Add your secret key here
    $recaptcha_verify = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
        'body' => array(
            'secret' => $recaptcha_secret,
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        )
    ));
    $recaptcha_result = json_decode(wp_remote_retrieve_body($recaptcha_verify), true);

    if (!$recaptcha_result['success']) {
        wp_send_json_error(['message' => 'reCAPTCHA validation failed. Please try again.']);
        return;
    }

    $current_user = wp_get_current_user();

    $comment_data = [
        'comment_post_ID' => absint($_POST['comment_post_ID']),
        'comment_content' => sanitize_text_field($_POST['comment']),
        'comment_author' => $current_user->display_name,
        'comment_author_email' => $current_user->user_email,
        'user_id' => $current_user->ID,
        'comment_parent' => isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0,
        'comment_approved' => 0, 
    ];

    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        $comment = get_comment($comment_id);
        $avatar = get_avatar($comment, 64); 
        $comment_author = $comment->comment_author;
        $comment_date = get_comment_date('', $comment_id);
        $comment_content = $comment->comment_content;

        wp_send_json_success([
            'message' => 'Your comment has been successfully submitted and is awaiting approval!',
            'avatar' => $avatar,
            'comment_author' => $comment_author,
            'comment_date' => $comment_date,
            'comment_content' => wpautop($comment_content),
        ]);
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

function handle_refresh_comments() {
    check_ajax_referer('submit_comment_nonce', 'nonce'); 

    $post_id = absint($_POST['post_id']); 
    if (!$post_id) {
        wp_send_json_error(['message' => 'Invalid post ID']);
    }

    $context = Timber::context();
    $context['grouped_comments'] = Timber::get_comments(['post_id' => $post_id]);

    $comments_html = Timber::compile('templates/comments-template.twig', $context);

    wp_send_json_success(['comments_html' => $comments_html]);
}
add_action('wp_ajax_refresh_comments', 'handle_refresh_comments');
add_action('wp_ajax_nopriv_refresh_comments', 'handle_refresh_comments');


add_filter('nonce_life', function() {
    return 24 * 60 * 60; 
});

function handle_like_comment() {
    check_ajax_referer('ajax_comment_rating_nonce', 'nonce'); 

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to like a comment']);
    }

    $comment_id = intval($_POST['comment_id']);
    $user_id = get_current_user_id();

    $likes_count = get_comment_meta($comment_id, 'likes_count', true) ?: 0;
    $dislikes_count = get_comment_meta($comment_id, 'dislikes_count', true) ?: 0;
    $liked_by = get_comment_meta($comment_id, 'liked_by', true) ?: []; 
    $disliked_by = get_comment_meta($comment_id, 'disliked_by', true) ?: []; 


    if (in_array($user_id, $liked_by)) {

        $liked_by = array_diff($liked_by, [$user_id]);
        $likes_count--;
    } else {

        $liked_by[] = $user_id;
        $likes_count++;

        if (in_array($user_id, $disliked_by)) {
            $disliked_by = array_diff($disliked_by, [$user_id]);
            $dislikes_count--;
        }
    }

    update_comment_meta($comment_id, 'liked_by', $liked_by);
    update_comment_meta($comment_id, 'disliked_by', $disliked_by);
    update_comment_meta($comment_id, 'likes_count', $likes_count);
    update_comment_meta($comment_id, 'dislikes_count', $dislikes_count);

    wp_send_json_success(['likes_count' => $likes_count, 'dislikes_count' => $dislikes_count]);
}
add_action('wp_ajax_like_comment', 'handle_like_comment');
add_action('wp_ajax_nopriv_like_comment', 'handle_like_comment');

function handle_dislike_comment() {
    check_ajax_referer('ajax_comment_rating_nonce', 'nonce'); 

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to dislike a comment']);
    }

    $comment_id = intval($_POST['comment_id']);
    $user_id = get_current_user_id();

    $likes_count = get_comment_meta($comment_id, 'likes_count', true) ?: 0;
    $dislikes_count = get_comment_meta($comment_id, 'dislikes_count', true) ?: 0;
    $liked_by = get_comment_meta($comment_id, 'liked_by', true) ?: []; 
    $disliked_by = get_comment_meta($comment_id, 'disliked_by', true) ?: []; 

    if (in_array($user_id, $disliked_by)) {

        $disliked_by = array_diff($disliked_by, [$user_id]);
        $dislikes_count--;
    } else {

        $disliked_by[] = $user_id;
        $dislikes_count++;

        if (in_array($user_id, $liked_by)) {
            $liked_by = array_diff($liked_by, [$user_id]);
            $likes_count--;
        }
    }

    update_comment_meta($comment_id, 'liked_by', $liked_by);
    update_comment_meta($comment_id, 'disliked_by', $disliked_by);
    update_comment_meta($comment_id, 'likes_count', $likes_count);
    update_comment_meta($comment_id, 'dislikes_count', $dislikes_count);

    wp_send_json_success(['likes_count' => $likes_count, 'dislikes_count' => $dislikes_count]);
}
add_action('wp_ajax_dislike_comment', 'handle_dislike_comment');
add_action('wp_ajax_nopriv_dislike_comment', 'handle_dislike_comment');
add_filter('nonce_life', function () {
    return 24 * 60 * 60; // 24 hours
});

function enable_comments_for_existing_cars_posts()
{

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

function create_rental_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'car_rentals'; 

    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            car_title VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            dob DATE NOT NULL,
            country VARCHAR(100) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            add_ons TEXT NOT NULL,
            total_cost DECIMAL(10, 2) NOT NULL,
            payment_method VARCHAR(20) NOT NULL,
            card_number VARCHAR(20) NOT NULL, 
            is_paid TINYINT(1) DEFAULT 0 NOT NULL, 
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
add_action('after_setup_theme', 'create_rental_table');


add_action('rest_api_init', function() {
    register_rest_route('internship/v1', '/save-booking', array(
        'methods' => 'POST',
        'callback' => 'save_booking',
        'permission_callback' => '__return_true', // Permissions
    ));
});

function save_booking(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'car_rentals';

    // Get the data from the request
    $params = $request->get_json_params();

    // Sanitize and extract the data
    $car_title = sanitize_text_field($params['carTitle']);
    $first_name = sanitize_text_field($params['firstName']);
    $last_name = sanitize_text_field($params['lastName']);
    $email = sanitize_email($params['email']);
    $phone = sanitize_text_field($params['phone']);
    $dob = sanitize_text_field($params['dob']['year'] . '-' . $params['dob']['month'] . '-' . $params['dob']['day']);
    $country = sanitize_text_field($params['country']);
    $start_date = sanitize_text_field($params['startDate']);
    $end_date = sanitize_text_field($params['endDate']);
    $add_ons = json_encode($params['addOns']);
    $total_cost = floatval($params['totalCost']);
    $payment_method = sanitize_text_field($params['paymentMethod']);
    $card_number = sanitize_text_field($params['cardNumber']);

    // Set the default value for is_paid based on the payment method
    $is_paid = ($payment_method == 'card') ? 1 : 0;

    // Insert the data into the database
    $wpdb->insert(
        $table_name,
        array(
            'car_title'      => $car_title,
            'first_name'     => $first_name,
            'last_name'      => $last_name,
            'email'          => $email,
            'phone'          => $phone,
            'dob'            => $dob,
            'country'        => $country,
            'start_date'     => $start_date,
            'end_date'       => $end_date,
            'add_ons'        => $add_ons,
            'total_cost'     => $total_cost,
            'payment_method' => $payment_method,
            'card_number'    => $card_number,
            'is_paid'        => $is_paid  
        )
    );

    // Check for errors
    if ($wpdb->last_error) {
        return new WP_REST_Response(array('status' => 'error', 'message' => $wpdb->last_error), 500);
    }

    return new WP_REST_Response(array('status' => 'success', 'message' => 'Booking saved successfully'), 200);
}


function car_rentals_menu() {
    add_menu_page(
        'Car Rentals',  
        'Car Rentals',  
        'manage_options',  
        'car-rentals', 
        'display_car_rentals_page',  
        'dashicons-media-spreadsheet', 
        30  
    );
}
add_action('admin_menu', 'car_rentals_menu');

function display_car_rentals_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'car_rentals';

    // Fetch the rental records from the database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Display the records 
    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Car Rentals</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>ID</th>
                <th>Car Title</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Date of Birth</th>
                <th>Country</th>
                <th>Rental Start</th>
                <th>Rental End</th>
                <th>Add-ons</th>
                <th>Total Cost</th>
                <th>Payment Method</th>
                <th>Card Number</th>
                <th>Payment Status</th>
            </tr>
          </thead>';
    echo '<tbody>';

    if (!empty($results)) {
        foreach ($results as $row) {
            $add_ons = json_decode($row->add_ons);

            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->car_title) . '</td>';
            echo '<td>' . esc_html($row->first_name) . ' ' . esc_html($row->last_name) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->phone) . '</td>';
            echo '<td>' . esc_html($row->dob) . '</td>';
            echo '<td>' . esc_html($row->country) . '</td>';
            echo '<td>' . esc_html($row->start_date) . '</td>';
            echo '<td>' . esc_html($row->end_date) . '</td>';
            echo '<td>';
            if (!empty($add_ons)) {
                foreach ($add_ons as $add_on) {
                    // Check if the add-on has a cost
                    $cost = isset($add_on->cost) ? '$' . esc_html($add_on->cost) : ''; // Prevent undefined cost error
                    echo esc_html($add_on->name) . ' ' . $cost . '<br>';
                }
            } else {
                echo 'No add-ons';
            }
            echo '</td>';
            echo '<td>$' . esc_html($row->total_cost) . '</td>';
            echo '<td>' . esc_html($row->payment_method) . '</td>';

            // Check if the payment method is cash or card
            if ($row->payment_method === 'cash') {
             echo '<td>N/A</td>';
            } else {
              $masked_card = (strlen($row->card_number) > 4) 
              ? substr($row->card_number, 0, 4) . str_repeat('*', strlen($row->card_number) - 4)
                  : $row->card_number;  // If card number is less than 4 digits, show it as is
             echo '<td>' . esc_html($masked_card) . '</td>';
                }
            echo '<td>';
            if ($row->payment_method == 'cash') {
                // Checkbox for cash payments
                $checked = $row->is_paid ? 'checked' : '';
                echo '<input type="checkbox" class="payment-status" data-id="' . esc_html($row->id) . '" ' . $checked . '> Paid';
            } else {
                // Already paid with card
                echo 'Paid';
            }
            echo '</td>';

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="13">No car rentals found</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    // Add script to handle checkbox updates via AJAX
    echo '<script>
   jQuery(document).ready(function($) {
    $(".payment-status").on("change", function() {
        var rentalId = $(this).data("id");
        var isChecked = $(this).is(":checked") ? 1 : 0;

        $.post(ajaxurl, {
            action: "update_payment_status",
            rental_id: rentalId,
            is_paid: isChecked
        }, function(response) {
            console.log(response); // Log the entire response
            if (response.success && response.data.message) {
                alert(response.data.message); // Properly access the message
            } else {
                alert("An error occurred while updating the payment status.");
            }
        });
    });
});
    </script>';
}

// Handle AJAX request to update payment status
add_action('wp_ajax_update_payment_status', 'update_payment_status');
function update_payment_status() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'car_rentals';

    $rental_id = intval($_POST['rental_id']);
    $is_paid = intval($_POST['is_paid']);

    // Update the 'is_paid' field
    $updated = $wpdb->update(
        $table_name,
        array('is_paid' => $is_paid),
        array('id' => $rental_id),
        array('%d'),
        array('%d')
    );

    // Check if the update was successful
    if ($updated === false) {
        wp_send_json_error(array('message' => 'Failed to update payment status.'));
    } else {
        wp_send_json_success(array('message' => 'Payment status updated.'));
    }
}

add_action('rest_api_init', function() {
    register_rest_route('internship/v1', '/unavailable-dates', array(
        'methods' => 'GET',
        'callback' => 'get_unavailable_dates',
        'permission_callback' => '__return_true',
    ));
});


function get_unavailable_dates(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'car_rentals';

    // Fetch all rental records
    $results = $wpdb->get_results("SELECT car_title, start_date, end_date FROM $table_name");

    $unavailable_dates = array();

    // Loop through each rental and add its date range to the unavailable dates array
    foreach ($results as $rental) {
        $car_title = $rental->car_title;
        $start_date = new DateTime($rental->start_date);
        $end_date = new DateTime($rental->end_date);
        $end_date->modify('+1 day'); // Make the day after unavailable as well

        // Create an array to store the dates for this rental period
        $dates = array();

        while ($start_date <= $end_date) {
            $dates[] = $start_date->format('Y-m-d');
            $start_date->modify('+1 day');
        }

        if (!isset($unavailable_dates[$car_title])) {
            $unavailable_dates[$car_title] = array();
        }

        $unavailable_dates[$car_title] = array_merge($unavailable_dates[$car_title], $dates);
    }

    // Remove any duplicate dates for each car and re-index
    foreach ($unavailable_dates as $car_title => $dates) {
        $unavailable_dates[$car_title] = array_values(array_unique($dates));
    }

    return new WP_REST_Response(array('status' => 'success', 'unavailable_dates' => $unavailable_dates), 200);
}