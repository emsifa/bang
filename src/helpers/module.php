<?php

/**
 * Get module file path
 * 
 * @param string $module 
 * @return string
 */
function module_path($module) {
    $filename = str_replace('.', '/', $module) . '.php';
    return config('path.module').'/'.$filename;
}

/**
 * Check wether module exists or not
 *
 * @param string $module
 * @return bool
 */
function has_module($module) {
    $module_path = module_path($module);
    return (file_exists($module_path) AND is_file($module_path));
}

/**
 * Call module
 *
 * @param string $module
 * @return mixed
 */
function call_module($module) {
    if ( ! has_module($module) ) {
        throw new \Exception("Module {$module} tidak tersedia", 404);
    }

    ob_start();
    $returned = require(module_path($module));
    $output = ob_get_clean();

    return !is_int($returned)? $returned : $output ?: null;
}