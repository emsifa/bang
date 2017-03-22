<?php 

/**
| =====================================================================================
| SETUP / PREPARATION
| =====================================================================================
| load file yang dibutuhkan, set konfigurasi, dsb
*/

// Persiapkan beberapa hal mendasar
// ------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load file-file yang dibutuhkan
// ------------------------------------------------------
require(__DIR__.'/app/helper.php');

// Set konfigurasi
// ------------------------------------------------------
config([
    // konfigurasi utama aplikasi
    'path.module'       => __DIR__.'/app/module',
    'path.template'     => __DIR__.'/app/template',
    'default_module'    => 'welcome',
    'template'          => 'layout.php',

    // konfigurasi database
    'database.username' => 'emsifa',
    'database.password' => 'emsifa',
    'database.name'     => 'db_indra',
]);

/**
| =====================================================================================
| HANDLING REQUEST
| =====================================================================================
| handle request dan menampilkan output final.
| Untuk banyak kasus, kamu tidak perlu mengubah kode dibawah ini
*/
try {
    // mengambil module yang di akses
    $module = get_request_path() ?: config('default_module');
    // jalankan module
    $output = call_module($module);

    // jika $output berupa array/object, kirim response JSON 
    if (is_array($output)) {
        response_json($output); 
    }

    $template = config('template');
    // masukkan $output kedalam template (jika diset pada config)
    if ($template) {
        $content = $output;
        ob_start();
        include(config('path.template').'/'.$template);
        $output = ob_get_clean();
    }

    echo (string) $output;
} catch (Exception $e) {
    $message = $e->getMessage();
    $code = $e->getCode();
    abort($message, $code);
}
