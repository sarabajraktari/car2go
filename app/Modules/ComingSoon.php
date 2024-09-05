<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class ComingSoon implements ModuleInterface {
    
    public static function getData($key) { //! Gets all the Comming Soon Message
        $flexibleContent = get_field('modules_list')[$key];
        return $flexibleContent;
    }

    public static function render($key, $data) { //! Render Module
        Setup::view('modules/ComingSoon.twig', [
            'data' => $data,
        ]);
    }
}