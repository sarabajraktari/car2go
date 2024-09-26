<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class LogIn implements ModuleInterface {

    public static function getData($key) {
        $flexibleContent = get_field('modules_list')[$key];

        // Initialize background image URL
        $background_image_url = '';
        if (isset($flexibleContent['backround_image'])) { // Note: Keep the field name correct based on ACF
            $background_image_url = $flexibleContent['backround_image']['url'];
        }

        // Check if the user is logged in
        $is_logged_in = is_user_logged_in();

        // Handle logout action
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            wp_logout();
            wp_redirect(home_url()); // Redirect after logout
            exit;
        }

        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form']) && !$is_logged_in) {
            $username = sanitize_text_field($_POST['username']);
            $password = sanitize_text_field($_POST['password']);

            // Validate inputs
            if (empty($username) || empty($password)) {
                $error = 'Username and password are required.';
            } else {
                // Prepare login credentials
                $creds = [
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => isset($_POST['remember']),
                ];

                // Try to sign the user in
                $user = wp_signon($creds, false);

                if (is_wp_error($user)) {
                    $error = 'Invalid username or password.';
                } else {
                    // Login successful, redirect to homepage or another page
                    wp_redirect(home_url());
                    exit;
                }
            }
        }

        return [
            'is_logged_in'   => $is_logged_in,
            'logout_url'     => wp_logout_url(home_url()), // Redirect to homepage after logout
            'background_image' => $background_image_url,   // Include the background image URL
            'error'          => $error ?? null,
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/LogIn.twig', [
            'data' => $data,
        ]);
    }
}
