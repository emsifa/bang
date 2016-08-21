<?php

/**
 * ----------------------------------------------------------------------
 * Module helper
 * ----------------------------------------------------------------------
 * Fungsi-fungsi berhubungan dengan module
 */

function module_path($module) {
    $filename = str_replace('.', '/', $module) . '.php';
    return config('module_path').'/'.$filename;
}


function has_module($module) {
    $module_path = module_path($module);
    if ( file_exists($module_path) AND is_file($module_path) ) {
        return true;
    } else {
        return false;
    }
}

function call_module($module) {
    if ( ! has_module($module) ) {
        throw new \Exception("Module {$module} tidak tersedia", 404);
    }

    ob_start();
    $returned = require(module_path($module));
    $output = ob_get_clean();

    return !is_int($returned)? $returned : $output ?: null;
}

/**
 * ----------------------------------------------------------------------
 * URL helper
 * ----------------------------------------------------------------------
 * Fungsi untuk generate url
 */
function base_url($path = null) {
    $base_url = config('base_url');
    if ( ! $base_url) {
        $base_url = 'http://'.str_replace('//','/',$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME']))));
    }

    return rtrim($base_url,'/').'/'.ltrim($path,'/');
}

function module_url($module, array $params = array()) {
    $params = http_build_query($params);
    return base_url($module) . ($params? '?'.$params : '');
}

/**
 * ----------------------------------------------------------------------
 * Config helper
 * ----------------------------------------------------------------------
 * set/get configuration
 */
function config($key, $value = null) {
    static $configs;

    if( ! $configs ) {
        $configs = array();
    }

    $args = func_get_args();

    if ( count($args) == 1 ) {
        if ( is_array($key) ) {
            // merge configs jika $key berupa array
            $configs = array_merge($configs, $key);
        } elseif ( is_string($key) ) {
            // kembalikan nilai konfigurasi
            return array_key_exists($key, $configs)? $configs[$key] : null;
        } else {
            throw new \Exception("Argumen '\$key' hanya dapat berupa array atau string.", 1);
        }
    } else {
        if ( !is_string($key) ) {
            throw new \Exception("Argumen '\$key' hanya dapat berupa array atau string.", 1);
        }

        $configs[$key] = $value;
    }
}

/**
 * ----------------------------------------------------------------------
 * Utility helper
 * ----------------------------------------------------------------------
 * Fungsi-fungsi lain yang akan sering digunakan pada aplikasi
 */
function input($key, $default = null) {
    $inputs = $_POST + $_GET;
    return isset($inputs[$key])? $inputs[$key] : $default;
}

function redirect($url, array $params = []) {
    if ( ! empty($params) ) {
        // resolve query string
        $expl = explode('?', $url, 2);
        $url_has_params = count($expl) > 1;
        if ($url_has_params) {
            parse_str($expl[1], $url_params);
            $params = array_merge($url_params, $params);
        }
        $url = $expl[0] . '?' . http_build_query($params);
    }

    header('Location: '.$url);
    exit;
}

function get_request_path() {
    if ( array_key_exists('PATH_INFO', $_SERVER) ) {
        $uri = $_SERVER['PATH_INFO'];
    } else {
        $file = ltrim($_SERVER['SCRIPT_NAME'],'/');
        $self = ltrim($_SERVER['PHP_SELF'], '/');
        $uri = preg_replace("#^".$file."#", '', $self);
    }

    return ltrim($uri, '/');
}
