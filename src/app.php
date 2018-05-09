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

// Load Requirement Files
// ------------------------------------------------------
require(__DIR__.'/helpers/router.php');
require(__DIR__.'/helpers/url.php');
require(__DIR__.'/helpers/config.php');
require(__DIR__.'/helpers/block.php');
require(__DIR__.'/helpers/utils.php');

// Set Configurations
// ------------------------------------------------------
config([
    'path.routes'       => __DIR__.'/routes',
    'path.templates'    => __DIR__.'/templates',
    'index_route'       => 'welcome',
    'template'          => 'layout.php',
]);
