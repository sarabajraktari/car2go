<?php 

if (is_404()) {
    $loader = new \Twig\Loader\FilesystemLoader(get_template_directory() . '/views/templates');
    $twig = new \Twig\Environment($loader);

    echo $twig->render('404.twig', ['template_directory_uri' => get_template_directory_uri()]);
}


?>
