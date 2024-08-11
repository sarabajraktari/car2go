<?php

namespace Internship\Modules;

use Internship\Interfaces\ModuleInterface;
use Internship\Includes\Setup;

class Footer implements ModuleInterface {

    public static function getData($key) {
        // Fetch data from ACF fields
        $flexibleContent = get_field('footer_section')[$key];
        return $flexibleContent;
    }

    public static function render($key, $data) {
        Setup::view('includes/footer.twig', [
            'data' => $data,
        ]);
    }
}

