<?php

function get_middlewares($route_file) {
    static $middlewares;

    $route_dir = dirname($route_file);

    if (!$middlewares) {
        $middlewares[] = [];
    }

    if (!isset($middlewares[$route_dir])) {
        $middlewares[$route_dir] = [];

        $routes_path = config('path.routes');
        $route_path = trim(str_replace($routes_path, '', $route_dir), '/');
        $segments = explode('/', $route_path);
        $path = $routes_path;
        if (is_file($path . '/_.php')) {
            $middlewares[$route_dir][] = $path . '/_.php';
        }
        foreach ($segments as $segment) {
            $path .= '/' . $segment;
            $middleware_file = $path . '/_.php';
            if (is_file($middleware_file)) {
                $middlewares[$route_dir][] = $middleware_file;
            }
        }
    }

    return $middlewares[$route_dir];
}

function load_middlewares($route_file) {
    static $loadeds;

    $route_dir = dirname($route_file);

    if (!is_array($loadeds) || !in_array($route_dir, $loadeds)) {
        $middlewares = get_middlewares($route_file);
        foreach ($middlewares as $middleware) {
            require($middleware);
        }
        $loadeds[] = $route_dir;
    }
}

function middleware_before($route_file) {
    load_middlewares($route_file);

    $routes_path = config('path.routes');
    $middlewares = get_middlewares($route_file);

    foreach ($middlewares as $middleware) {
        $route_path = ltrim(dirname(str_replace($routes_path, '', $middleware)), '/');
        $namespace = 'routes\\' . str_replace('/', "\\", $route_path);
        $callback = $namespace . '\\before';
        if (function_exists($callback)) {
            $output = call_user_func($callback);
            if ($output) {
                return $output;
            }
        }
    }
}

function middleware_after($route_file, $output) {
    load_middlewares($route_file);

    $routes_path = config('path.routes');
    $middlewares = array_reverse(get_middlewares($route_file));

    foreach ($middlewares as $middleware) {
        $route_path = ltrim(dirname(str_replace($routes_path, '', $middleware)), '/');
        $namespace = 'routes\\' . str_replace('/', "\\", $route_path);
        $callback = $namespace . '\\after';

        if (function_exists($callback)) {
            $output = call_user_func($callback, $output);
        }
    }

    return $output;
}
