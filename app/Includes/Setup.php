<?php

namespace Internship\Includes;

use Internship\Interfaces\ModuleInterface;
use Internship\Menus\Footer;

class Setup {
    public static $loader;
    public static $twig;

    public static function renderPage() { //! Render the page.twig with all the modules
        self::addToTwig();

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

        $footerData = Footer::getData();
        echo self::$twig->render('page.twig', [
            'modules' => $modules,
            'footer' => $footerData
        ]);
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

        self::$twig->addFunction(new \Twig\TwigFunction('renderModule', function ($module, $key) {
            $moduleClass = 'Internship\\Modules\\' . $module['type'];
            $moduleClass::render($key, $module['data']);
        }, ['is_safe' => ['html']]));
    }
}