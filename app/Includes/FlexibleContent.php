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
    
    public function getFooterSection(): array
    {
        $footer_section = get_field('footer_section', 'option');
        
        if ($footer_section) {
            return $footer_section;
        } else {
            error_log('Flexible content "footer_section" is not available or empty.');
            return [];
        }
    }
}
