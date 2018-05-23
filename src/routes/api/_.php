<?php

namespace routes\api;

function after($output) {
    return is_array($output) ? array_merge(['status' => 'ok'], $output) : [
        'status' => 'ok',
        'message' => $output
    ];
}
