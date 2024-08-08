<?php

namespace Internship\Modules;

use Internship\Interfaces\ModuleInterface;
use Internship\Includes\Setup;

class CarTypes implements ModuleInterface {

    public static function getData($key) {
        // Fetch data from ACF fields
        $flexibleContent = get_field('modules_list')[$key];
        return [
            'title_and_description' => $flexibleContent['title_and_description'],
            'cartypes_list' => $flexibleContent['cartypes_list'],
            'see_all_cars_link' => $flexibleContent['see_all_cars_link'],
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/Cartypes.twig', [
            'data' => $data,
        ]);
    }
}

