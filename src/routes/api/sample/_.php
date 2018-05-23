<?php

namespace routes\api\sample;

function after($output) {
    return [
        'message' => $output
    ];
}
