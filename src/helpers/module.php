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