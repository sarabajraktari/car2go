<?php

namespace Internship\Interfaces;

interface ModuleInterface {
    public static function getData($key);
    public static function render($key, $data);
}
