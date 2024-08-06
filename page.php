<?php
define('THEME', get_template_directory());

use Internship\Includes\Setup;

$context = [
    'posts' => get_posts(),
    // Add other context variables as needed
];

Setup::renderPage('templates/authors.twig', $context);
