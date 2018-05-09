<?php 

/**
| =====================================================================================
| HANDLING REQUEST
| =====================================================================================
| handling request and print final output.
| For many cases, you don't need to edit code belows
*/

// Bootstraping application
require(__DIR__.'/src/app.php');

try {
    $method = request_method();
    $route  = request_path() ?: config('index_route');
    $output = call_route($method, $route);

    // handle output types, in some case you may add/modify this
    switch ( strtoupper(gettype($output)) ) {
        case 'ARRAY': 
        case 'OBJECT': {
            header('Content-Type: application/json');
            $output = json_encode($output); 
            break;
        }

        case 'STRING': {
            $template = config('template');
            if ( $template ) {
                list($output, $blocks) = parse_blocks($output);
                foreach($blocks as $block => $block_content) {
                    block($block, $block_content);
                }
                $content = $output;
                ob_start();
                include(config('path.templates').'/'.$template);
                $output = ob_get_clean();
            }
            break;
        }
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