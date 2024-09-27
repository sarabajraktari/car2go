<?php

namespace Internship\Menus;

use Internship\Includes\Setup;

class Header {

    public static function getData() {
        $header = get_field('header_section','option');
        return $header;
    }

    public static function render($data){
        Setup::view('navigation/HeaderNavigation.twig',[
            'data' => $data,
        ]);
    }

} 