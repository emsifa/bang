<?php

/**
 * Get input from $_GET and $_POST
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function input($key, $default = null) {
    $inputs = $_POST + $_GET;
    return isset($inputs[$key])? $inputs[$key] : $default;
}

/**
 * Redirect to given url
 * 
 * @param string $url
 * @param array $params
 * @return void
 */
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

/**
 * Get request path info
 *
 * @return string
 */
function request_path() {
    if ( array_key_exists('PATH_INFO', $_SERVER) ) {
        $uri = $_SERVER['PATH_INFO'];
    } else {
        $file = ltrim($_SERVER['SCRIPT_NAME'],'/');
        $self = ltrim($_SERVER['PHP_SELF'], '/');
        $uri = preg_replace("#^".$file."#", '', $self);
    }

    return trim($uri, '/');
}

/**
 * Get request method
 *
 * @return string
 */
function request_method() {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST' && input('_method')) {
        return input('_method');
    } else {
        return $method;
    }
}

/** 
 * Get uri segment
 *
 * @param int $index
 * @return string
 */
function uri_segment($index) {
    $segments = explode('/', request_path());
    if ($index < 0) {
        $index = count($segments) + $index;
    }

    return isset($segments[$index]) ? $segments[$index] : null;
}