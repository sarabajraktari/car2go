<?php

namespace Internship\Modules;

use Internship\Interfaces\ModuleInterface;
use Internship\Includes\Setup;

class AddNumbers implements ModuleInterface {

    public static function getData($key) {
        // Fetch data from ACF fields
        $flexibleContent = get_field('modules_list')[$key];
        return [
            'title_and_description' => $flexibleContent['title_and_description'],
            'numbers' => $flexibleContent['numbers'],
            'image_background1' => $flexibleContent['image_background1'] ?? null, 
            'image_background2' => $flexibleContent['image_background2'] ?? null, 

        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/AddNumbers.twig', [
            'data' => $data,
        ]);
    }
}