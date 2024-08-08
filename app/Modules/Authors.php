<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class Authors implements ModuleInterface {
    
    public static function getData($key) {
        $flexibleContent = get_field('modules_list')[$key];
        return $flexibleContent;
    }

    public static function render($key, $data) {
        Setup::view('modules/Authors.twig', [
            'data' => $data,
        ]);
    }
}
