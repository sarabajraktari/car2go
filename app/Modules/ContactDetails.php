<?php

namespace Internship\Modules;

use Internship\Interfaces\ModuleInterface;
use Internship\Includes\Setup;

class ContactDetails implements ModuleInterface {

    public static function getData($key) {
        // Fetch data from ACF fields
        $flexibleContent = get_field('modules_list')[$key];
        return [
            'details' => $flexibleContent['details'],
        ];
    }

    public static function render($key, $data) {
        Setup::view('modules/ContactDetails.twig', [
            'data' => $data,
        ]);
    }
}