<?php

namespace Internship\Modules;

use Internship\Interfaces\ModuleInterface;
use Internship\Includes\Setup;

class HowItWorks implements ModuleInterface {

    public static function getData($key) {
        // Fetch data from ACF fields
        $flexibleContent = get_field('modules_list')[$key];
        return [
            'title_and_description' => $flexibleContent['title_and_description'],
            'steps' => $flexibleContent['steps'],
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/HowItWorks.twig', [
            'data' => $data,
        ]);
    }
}