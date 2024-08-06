<?php
define('THEME', get_template_directory());

use Internship\Includes\Setup;
    wp_nav_menu(array('theme_location'=>'primary')); 

$context = [
    'posts' => get_posts(),
    // Add other context variables as needed
];

Setup::renderPage('templates/authors.twig', $context);
