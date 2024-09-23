<?php

namespace Internship\Menus;

use Internship\Includes\Setup;

class Header {

    public static function getData() {
        $header = get_field('header_section', 'option'); // Get header data

        // Return both header and login/logout data
        return array_merge($header, [
            'is_logged_in' => is_user_logged_in(),  // Check if user is logged in
            'login_url'    => site_url('/log-in'),   // URL to login page
            'logout_url'   => wp_logout_url(home_url()),  // Logout and immediately redirect to homepage
        ]);
    }

    public static function render($data){
        Setup::view('navigation/HeaderNavigation.twig', [
            'data' => $data,
        ]);
    }

}
