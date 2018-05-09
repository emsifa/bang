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
 * Get url to route
 * 
 * @param string $route
 * @param array $params
 * @return string
 */
function route_url($route, array $params = array()) {
    $params = http_build_query($params);
    $query_string = ($params? '?'.$params : '');
    if ($index_file = config('index_file')) {
        return base_url($index_file.'/'.$route) . $query_string;
    } else {
        return base_url($route) . $query_string;
    }
}
