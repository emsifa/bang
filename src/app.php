<?php

/**
| =====================================================================================
| BOOTSTRAPING APP REQUIREMENTS
| =====================================================================================
| load requirement files, setup configuration, initialize database, etc
*/

// Some Preparations
// ------------------------------------------------------
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Application Helpers
// ------------------------------------------------------
$helpers = [
    'middleware',
    'router',
    'url',
    'config',
    'block',
    'utils'
];
foreach ($helpers as $helper) {
    require(__DIR__ . '/helpers/'. $helper .'.php');
}

// Set Configurations
// ------------------------------------------------------
config(require(__DIR__.'/configs.php'));
