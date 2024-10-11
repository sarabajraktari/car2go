<?php

namespace Internship\Includes;

use Internship\Interfaces\ModuleInterface;
use Internship\Menus\Header;
use Internship\Menus\Footer;
use Internship\PostTypes\Car;
use Internship\PostTypes\Author;
use Internship\PostTypes\TaxonomyData;
use Internship\PostTypes\RentNow;

class Setup {
    public static $loader;
    public static $twig;

    public static function renderPage($template = 'views/page.twig', $context = []) {
        self::addToTwig();

        // 404 Check
        if (is_404()) {
            $template = 'templates/404.twig'; // Use 404 template
        }

        $brands = TaxonomyData::getTaxonomyData('brand');
        $cities = TaxonomyData::getTaxonomyData('city');

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
        
        $commentsOpen = false; 
        $comments = []; 
        $grouped_comments = [];
        
        if (is_singular('cars')) { 
            $isSingleCarPage = true;
            $carSlug = get_post_field('post_name', get_post());
            $carData = Car::getSingleCarData($carSlug);
        
            $commentsOpen = comments_open();
            $comments = get_comments([
                'post_id' => get_the_ID(),
                'status' => 'all',
            ]);
        
            foreach ($comments as &$comment) {
                $comment->avatar = get_avatar($comment->comment_author_email, 64);
                $comment->reply_link = get_comment_reply_link([
                    'depth' => 1,
                    'max_depth' => 5,
                    'reply_text' => 'Reply'
                ], $comment->comment_ID);
        
                $comment->is_approved = ($comment->comment_approved == '1');
                $comment->like_count = get_comment_meta($comment->comment_ID, 'likes_count', true) ?: 0;
                $comment->dislike_count = get_comment_meta($comment->comment_ID, 'dislikes_count', true) ?: 0;
            }
        
            usort($comments, function($a, $b) {
                $a_net_score = $a->like_count - $a->dislike_count;
                $b_net_score = $b->like_count - $b->dislike_count;
        
                if ($b_net_score !== $a_net_score) {
                    return $b_net_score - $a_net_score;
                }
        

                if ($b->like_count !== $a->like_count) {
                    return $b->like_count - $a->like_count;
                }
        
                return $a->dislike_count - $b->dislike_count;
            });
        
            $grouped_comments = [];
            foreach ($comments as &$comment) {
                if ($comment->comment_parent == 0) {
                    $grouped_comments[$comment->comment_ID] = ['comment' => $comment, 'replies' => []];
                } else {
                    if (isset($grouped_comments[$comment->comment_parent])) {
                        $grouped_comments[$comment->comment_parent]['replies'][] = $comment;
                    } else {
                        $grouped_comments[$comment->comment_parent] = ['comment' => null, 'replies' => [$comment]];
                    }
                }
            }
        }
        
        
        $current_user = wp_get_current_user();
        $is_user_logged_in = is_user_logged_in();
        $actual_user_role = $current_user->roles;
        error_log('Current User Roles: ' . print_r($actual_user_role, true));

        $login_url = wp_login_url();
        $register_url = wp_registration_url();
            

        $Author = false;
        $authorData = null;

        if(is_singular('authors')) {
            $Author = true;
            $authorSlug = get_post_field('post_name', get_post());
            $authorData = Author::getSingleAuthorData($authorSlug);
        }

        $isSingleRentNowPage = false;
        $rentNowData = null;

        if (is_singular('rent_now')) {
            $isSingleRentNowPage = true;
            $rentNowSlug = get_post_field('post_name', get_post());
            $rentNowData = RentNow::getSingleRentNowData($rentNowSlug);
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
            #'cars' => $cars,
            'brands' => $brands,
            'cities' => $cities,
            'rent_now' => $rentNowData,
            'is_single_rent_now_page' => $isSingleRentNowPage,
            'comments_open' => $commentsOpen,
            'comments' => $comments, 
            'grouped_comments' => $grouped_comments, 
            'is_user_logged_in' => $is_user_logged_in,
            'user' => [
                'ID' => $current_user->ID,
                'display_name' => $current_user->display_name,
                'user_email' => $current_user->user_email,
            ],
            'login_url' => $login_url,
            'register_url' => $register_url,
            'actual_user_role' =>$actual_user_role,
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

        $brands = TaxonomyData::getTaxonomyData('brand');
        $cities = TaxonomyData::getTaxonomyData('city');

        self::$twig->addGlobal('brands', $brands);
        self::$twig->addGlobal('cities', $cities);

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

        self::$twig->addFunction(new \Twig\TwigFunction('get_permalink', function ($post_id = null) {
            if ($post_id) {

                return get_permalink($post_id);
            } else {

                $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                return $current_url;
            }
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('url_contains', function ($url, $substring) {
            return strpos($url, $substring) !== false;
        }));

        self::$twig->addFunction(new \Twig\TwigFunction('comment_form', function () {
            ob_start();
            comment_form([
                'submit_button' => '<button class="%1$s-button-c">Post your Comment</button>',
                'submit_field' => '<div class="form-submit-wrapper flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">%1$s<div class="g-recaptcha mt-4 md:mt-0 mx-auto md:mx-0" data-sitekey="YOUR_RECAPTCHA_SITE_KEY_HERE_V2"></div>%2$s</div>',
                'comment_field' => '<div class="comment-text-h mb-4"><label for="comment" class="block text-sm font-medium">Your Comment:</label><textarea id="comment" name="comment" class="comment-text-area-c" rows="6" required></textarea></div>',
                'fields' => [
                    'author' => '<div class="mb-4"><label for="author" class="block text-sm font-medium text-gray-700">Name</label><input type="text" id="author" name="author" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></div>',
                    'email' => '<div class="mb-4"><label for="email" class="block text-sm font-medium text-gray-700">Email</label><input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></div>',
                ],
                'logged_in_as' => '<p class="logged-in-as mb-4 text-sm">You are logged in as <a href="' . esc_url(admin_url('profile.php')) . '" class="comment-text-b">' . wp_get_current_user()->display_name . '</a>. <a href="' . esc_url(wp_logout_url()) . '" class="comment-text-r">Log out?</a></p>',
                'comment_notes_before' => '<p class="comment-notes-before text-sm text-gray-500">Required fields are marked <span class="text-red-500">*</span></p>',
            ]);
            return ob_get_clean();
        }, ['is_safe' => ['html']] ));
        
        self::$twig->addFunction(new \Twig\TwigFunction('renderModule', function ($module, $key) {
            $moduleClass = 'Internship\\Modules\\' . $module['type'];
            $moduleClass::render($key, $module['data']);
        }, ['is_safe' => ['html']]));
    }
}
