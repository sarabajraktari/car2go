<?php 
namespace Internship\Includes;

class FlexibleContent {

    public function getModules(): array
    {
        $modules_list = get_field('modules_list');
        
        if ($modules_list) {
            return $modules_list;
        } else {
            error_log('Flexible content "modules_list" is not available or empty.');
            return []; 
        }
    }
}
