<?php 

// index.php : this is core file where any request on app should pointed to this file

/**
| =====================================================================================
| SETUP / PREPARATION
| =====================================================================================
| load requirement files, setup configuration, initialize database, etc
*/

// Some Preparations
// ------------------------------------------------------
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Requirement Files
// ------------------------------------------------------
require(__DIR__.'/app/helper.php');

// Set Configs
// ------------------------------------------------------
config([
    'module_path'       => __DIR__.'/app/module',
    'template_path'     => __DIR__.'/app/template',
    'default_module'    => 'welcome',
    'template'          => 'layout.php',
]);

/**
| =====================================================================================
| HANDLING REQUEST
| =====================================================================================
| handling request and print final output.
| For many cases, you don't need to edit code belows
*/

try {
    $module = get_request_path() ?: config('default_module');
    $output = call_module($module);

    // handle output types, in some case you may add/modify this
    switch ( strtoupper(gettype($output)) ) {
        case 'ARRAY': 
        case 'OBJECT':
            header('Content-Type: application/json');
            $output = json_encode($output); 
            break;

        case 'STRING':
            $template = config('template');
            if ( $template ) {
                $content = $output;
                ob_start();
                include(config('template_path').'/'.$template);
                $output = ob_get_clean();
            }
            break;
    }
} catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
    if ($code < 100 OR $code > 599) {
        $code = 500;
    }
    http_response_code($code);
    $output = "<h4>Error {$code}</h4>{$message}";    
}

echo (string) $output;