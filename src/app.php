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
require(__DIR__.'/helpers/module.php');
require(__DIR__.'/helpers/url.php');
require(__DIR__.'/helpers/config.php');
require(__DIR__.'/helpers/block.php');
require(__DIR__.'/helpers/utils.php');

// Set Configurations
// ------------------------------------------------------
config([
    'path.module'       => __DIR__.'/modules',
    'path.template'     => __DIR__.'/templates',
    'default_module'    => 'welcome',
    'template'          => 'layout.php',
]);
