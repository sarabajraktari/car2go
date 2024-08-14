<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class CustomBanner implements ModuleInterface {
    
    public static function getData($key) { //! Gets all the Banner Data
        $flexibleContent = get_field('modules_list')[$key];
        return $flexibleContent;
    }

    public static function render($key, $data) { //! Render Module
        Setup::view('modules/CustomBanner.twig', [
            'data' => $data,
        ]);
    }
}