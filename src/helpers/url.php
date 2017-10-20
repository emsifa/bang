<?php

/**
 * Get base url to a path
 * 
 * @param string $path
 * @return string
 */
function base_url($path = null) {
    $base_url = config('base_url');
    if ( ! $base_url) {
        $base_url = 'http://'.str_replace('//','/',$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME']))));
    }

    return rtrim($base_url,'/').'/'.ltrim($path,'/');
}

/**
 * Get url to module
 * 
 * @param string $module
 * @param array $params
 * @return string
 */
function module_url($module, array $params = array()) {
    $params = http_build_query($params);
    $query_string = ($params? '?'.$params : '');
    if ($index_file = config('index_file')) {
        return base_url($index_file.'/'.$module) . $query_string;
    } else {
        return base_url($module) . $query_string;
    }
}
