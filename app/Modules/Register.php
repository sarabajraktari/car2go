<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class Register implements ModuleInterface {

    public static function getData($key) {
        // Get flexible content
        $flexibleContent = get_field('modules_list')[$key];

        // Initialize background image URL
        $background_image_url = '';
        if (isset($flexibleContent['backround_image'])) { // Note the typo: backround_image (from your data)
            $background_image_url = $flexibleContent['backround_image']['url'];
        }

        // Initialize error and success messages
        $error = null;
        $success = null;

        // Check if the form was submitted
        if (isset($_POST['add_user_form'])) {
            $username = sanitize_text_field($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = sanitize_text_field($_POST['password']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $subscribe_newsletter = isset($_POST['subscribe_newsletter']) ? 1 : 0; 
            
            // Automatically set the role to 'subscriber'
            $role = 'subscriber'; 

            // Check if the username or email already exists
            if (username_exists($username) || email_exists($email)) {
                $error = 'Username or email already exists.';
            } else {
                // Insert new user with the default 'subscriber' role
                $userdata = [
                    'user_login'    => $username,
                    'user_email'    => $email,
                    'user_pass'     => $password,
                    'role'          => $role, // Automatically set to 'subscriber'
                    'first_name'    => $first_name,
                    'last_name'     => $last_name,
                ];

                $user_id = wp_insert_user($userdata);

                // Check if any errors occurred
                if (is_wp_error($user_id)) {
                    $error = $user_id->get_error_message();
                } else {

                    update_user_meta($user_id, 'subscribe_newsletter', $subscribe_newsletter);
                    
                    // Log the user in after successful registration
                    $creds = [
                        'user_login'    => $username,
                        'user_password' => $password,
                        'remember'      => true, // Remember the user
                    ];
                    $user = wp_signon($creds, false);

                    if (is_wp_error($user)) {
                        $error = $user->get_error_message();
                    } else {
                        // Redirect to homepage or another page after login
                        wp_redirect(home_url());
                        exit;
                    }

                    $success = 'User created and logged in successfully!';
                }
            }
        }

        // Return data to be used in the Twig template
        return [
            'background_image' => $background_image_url, // Correct background image URL
            'error' => $error,
            'success' => $success,
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/Register.twig', [
            'data' => $data,
        ]);
    }
}
