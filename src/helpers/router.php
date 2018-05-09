<?php

/**
 * Get route file path
 *
 * @param string $uri
 * @param array $options
 * @return string|null
 */
function find_route($method, $uri, array $options = []) {
    $method = strtolower($method);
    if ($method == 'head') {
        $method = 'get';
    }
    
    $uri = trim($uri, '/');
    $options = array_merge([
        'index' => 'index',
        'base_dir' => config('path.routes'),
        'extension' => 'php',
        'param_prefix' => '_',
        'param_suffix' => '_',
        'matchers' => []
    ], $options);

    $param_prefix = $options['param_prefix'];
    $param_suffix = $options['param_suffix'];
    $extension = $options['extension'];
    $index = $options['index'];
    $index_file = $index.'.'.$extension;
    $matchers = array_merge([
        'any' => '/^[a-z0-9_-]+$/i',
        'num' => '/^\d+$/i',
        'alpha' => '/^[a-z]+$/i',
        'alphanum' => '/^[a-z0-9]+$/i',
    ], $options['matchers']);

    $segments = explode('/', $uri);
    $path = rtrim($options['base_dir'], '/');
    $n = count($segments);

    $segment_file = function ($path, $segment, $last = false) use (
        &$method, 
        &$extension, 
        &$matchers, 
        &$param_prefix, 
        &$param_suffix,
        &$index,
        &$index_file
    ) {
        $segment_dir = "{$path}/{$segment}";
        if ($last) {
            $filepaths = [
                "{$path}/{$method}({$segment}).{$extension}",
                "{$segment_dir}.{$extension}",
                "{$segment_dir}/{$method}({$index}).{$extension}",
                "{$segment_dir}/{$index_file}"
            ];
        } else {
            $filepaths = [
                "{$segment_dir}"
            ];
        }

        foreach ($filepaths as $filepath) {
            if (file_exists($filepath)) return $filepath;
        }

        // Find dynamic path
        foreach ($matchers as $key => $regex) {
            $match = (bool) preg_match($regex, $segment);
            if (!$match) continue;

            $key_segment = "{$param_prefix}{$key}{$param_suffix}";
            $segment_path = "{$path}/{$key_segment}";
            if ($last) {
                $filepaths = [
                    "{$path}/{$method}($key_segment).{$extension}",
                    "{$segment_path}.{$extension}",
                    "{$segment_path}/{$method}({$index}).{$extension}",
                    "{$segment_path}/{$index_file}",
                ];
            } else {
                $filepaths = [$segment_path];
            }

            foreach ($filepaths as $filepath) {
                if (file_exists($filepath)) return $filepath;
            }
        }

        return null;
    };

    while ($segment = array_shift($segments)) {
        $is_last = count($segments) === 0;
        $path = $segment_file($path, $segment, $is_last);
        if (!$path) {
            return null;
        }
    }
    return $path;
}

/**
 * Check wether route exists or not
 *
 * @param string $route
 * @return bool
 */
function has_route($method, $route, array $options = []) {
    return !is_null(find_route($method, $route, $options));
}

/**
 * Call route
 *
 * @param string $route
 * @return mixed
 */
function call_route($method, $route_uri, array $options = []) {
    $route_file = find_route($method, $route_uri, $options);
    if ( ! $route_file ) {
        throw new \Exception("Route {$route_uri} is not available.", 404);
    }

    ob_start();
    $returned = require($route_file);
    $output = ob_get_clean();

    return !is_int($returned)? $returned : $output ?: null;
}