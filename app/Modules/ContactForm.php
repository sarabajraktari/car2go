<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class ContactForm implements ModuleInterface {
    
    public static function getData($key) { //! Gets all the ContactForm Data
        $flexibleContent = get_field('modules_list')[$key];
        return $flexibleContent;
    }

    public static function render($key, $data) { //! Render Module
        Setup::view('modules/ContactForm.twig', [
            'data' => $data,
        ]);
    }
}