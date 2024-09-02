<?php

namespace Internship\Includes;

use Internship\Interfaces\ModuleInterface;
use Internship\Menus\Header;
use Internship\Menus\Footer;
use Internship\PostTypes\Car;
use Internship\PostTypes\Author;


class Setup {
    public static $loader;
    public static $twig;

    public static function renderPage($template = 'views/page.twig', $context = []) {
        self::addToTwig();

        // Kontrollo nëse është një faqe 404
        if (is_404()) {
            $template = 'templates/404.twig'; // Përdor shabllonin 404 nëse është faqe 404
        }

        $modules = [];
        $flexible_content = get_field('modules_list');

        if ($flexible_content) {
            foreach ($flexible_content as $key => $layout) {
                $moduleType = ucfirst($layout['acf_fc_layout']);
                $moduleClass = 'Internship\\Modules\\' . $moduleType;
                if (class_exists($moduleClass) && in_array(ModuleInterface::class, class_implements($moduleClass))) {
                    $moduleData = $moduleClass::getData($key);
                    $modules[] = [
                        'type' => $moduleType,
                        'key' => $key,
                        'data' => $moduleData
                    ];
                } else {
                    error_log('Module class ' . $moduleClass . ' not found or does not implement ModuleInterface.');
                }
            }
        }

        $isSingleCarPage = false;
        $carData = null;
    
        if (is_singular('cars')) { 
            $isSingleCarPage = true;
            $carSlug = get_post_field('post_name', get_post());
            $carData = Car::getSingleCarData($carSlug); 
        } 

        $Author = false;
        $authorData = null;

        if(is_singular('authors')) {
            $Author = true;
            $authorSlug = get_post_field('post_name', get_post());
            $authorData = Author::getSingleAuthorData($authorSlug);
        }


        $headerData = Header::getData(); 
        $footerData = Footer::getData();
        echo self::$twig->render($template, array_merge($context, [
            'modules' => $modules,
            'header' => $headerData,
            'footer' => $footerData,
            'car' => $carData, 
            'is_single_car_page' => $isSingleCarPage,
            'author' => $authorData,
            'is_author_page' => $Author,
        ]));
    }

    public static function view($template, $data = []) {
        self::addToTwig();
        echo self::$twig->render($template, $data);
    }

    public static function addToTwig() {
        self::$loader = new \Twig\Loader\FilesystemLoader(THEME . '/views');
        self::$twig = new \Twig\Environment(self::$loader, [
            'allow_callables' => true,
        ]);

        self::$twig->addFunction(new \Twig\TwigFunction('wp_head', function () {
            return wp_head();
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('wp_footer', function () {
            return wp_footer();
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('wp_title', function () {
            return wp_title('', false);
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('language_attributes', function () {
            return get_language_attributes();
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('bloginfo', function ($show) {
            return get_bloginfo($show);
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('body_class', function () {
            return join(' ', get_body_class());
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('dd', function ($item) {
            ob_start();
            var_dump($item);
            return ob_get_clean();
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('is_front_page', function () {
            return is_front_page();
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('renderModule', function ($module, $key) {
            $moduleClass = 'Internship\\Modules\\' . $module['type'];
            $moduleClass::render($key, $module['data']);
        }, ['is_safe' => ['html']]));
    }
}
