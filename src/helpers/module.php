<?php

/**
 * Get module file path
 *
 * @param string $uri
 * @param array $options
 * @return string|null
 */
function find_module($method, $uri, array $options = []) {
    $method = strtolower($method);
    if ($method == 'head') {
        $method = 'get';
    }
    
    $uri = trim($uri, '/');
    $options = array_merge([
        'index' => 'index',
        'base_dir' => config('path.module'),
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
    while ($segment = array_shift($segments)) {
        $next_path = "{$path}/{$segment}";
        $is_last = (count($segments) == 0);

        if (!$is_last && is_dir($next_path)) {
            $path = $next_path;
            continue;
        }

        if ($is_last) {
            foreach ([
                "{$path}/{$method}({$segment}).{$extension}",
                "{$next_path}.{$extension}",
                "{$next_path}/{$method}({$index}).{$extension}",
                "{$next_path}/{$index_file}"
            ] as $filepath) {
                if (is_file($filepath)) {
                    return $filepath;                
                }
            }
        }

        $result = null;
        foreach ($matchers as $key => $regex) {
            $match = (bool) preg_match($regex, $segment);
            if (!$match) continue;

            $key_segment = "{$param_prefix}{$key}{$param_suffix}";
            $next_path = "{$path}/{$key_segment}";
            if (!$is_last && is_dir($next_path)) {
                $next_uri = implode("/", $segments);
                if ($result = find_module($method, $next_uri, array_merge($options, ['base_dir' => $next_path]))) {
                    return $result;
                }
            } elseif($is_last) {
                $files = [
                    "{$path}/{$method}($key_segment).{$extension}",
                    "{$next_path}.{$extension}",
                    "{$next_path}/{$method}({$index}).{$extension}",
                    "{$next_path}/{$index_file}",
                ];
                foreach ($files as $file) {
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }
    }
}

/**
 * Check wether module exists or not
 *
 * @param string $module
 * @return bool
 */
function has_module($method, $module, array $options = []) {
    $find_module = find_module($method, $module, $options);
    return (file_exists($find_module) AND is_file($find_module));
}

/**
 * Call module
 *
 * @param string $module
 * @return mixed
 */
function call_module($method, $module_uri, array $options = []) {
    $module_file = find_module($method, $module_uri, $options);
    if ( ! $module_file ) {
        throw new \Exception("Module {$module_uri} tidak tersedia", 404);
    }

    ob_start();
    $returned = require($module_file);
    $output = ob_get_clean();

    return !is_int($returned)? $returned : $output ?: null;
}