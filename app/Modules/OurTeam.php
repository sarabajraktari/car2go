<?php

namespace Internship\Modules;

use Internship\Includes\Setup;
use Internship\Interfaces\ModuleInterface;

class OurTeam implements ModuleInterface {
    
    public static function getData($key) { 
        // Fetch the data from the 'our_team' repeater field within the 'OurTeam' layout
        $flexibleContent = get_field('modules_list')[$key];
        return [
            'our_team' => $flexibleContent['our_team'], // Fetch the 'our_team' repeater
        ];
    }

    public static function render($key, $data) { 
        // Render the Twig view with the data
        Setup::view('modules/OurTeam.twig', [
            'data' => $data['our_team'], // Pass 'our_team' repeater data to Twig
        ]);
    }
}
