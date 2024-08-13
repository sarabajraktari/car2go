<?php

namespace Internship\Menus;

use Internship\Includes\Setup;

class Footer {
    
    public static function getData() {
        $footer = get_field('footer_section','option');
        return $footer;
    }

    public static function render($data){
        Setup::view('navigation/FooterNavigation.twig',[
            'data' => $data,
        ]);
    }
  
}