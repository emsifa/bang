<?php

/**
 * Mengambil path dari module
 *
 * @param string $module nama module
 * @return string path dari module
 */
function module_path($module) {
    $filename = str_replace('.', '/', $module) . '.php';
    return config('path.module').'/'.$filename;
}

/**
 * Mengecek ketersediaan module
 *
 * @param string $module nama module
 * @return boolean ada/tidak
 */
function has_module($module) {
    $module_path = module_path($module);
    if (file_exists($module_path) AND is_file($module_path)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Menjalankan module
 *
 * @param string $module nama module
 * @return mixed
 */
function call_module($module) {
    if (! has_module($module)) {
        throw new \Exception("Module {$module} tidak tersedia", 404);
    }

    ob_start();
    $returned = require(module_path($module));
    $output = ob_get_clean();

    return !is_int($returned)? $returned : $output ?: null;
}

/**
 * Generate url berdasarkan base url
 *
 * @param string $path
 * @return string url hasil generate
 */
function base_url($path = null) {
    $base_url = config('base_url');
    if (! $base_url) {
        $base_url = 'http://'.str_replace('//','/',$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME']))));
    }

    return rtrim($base_url,'/').'/'.ltrim($path,'/');
}

/**
 * Generate module url berdasarkan base url
 *
 * @param string $module nama module
 * @param array $params query params
 * @return string url module
 */
function module_url($module, array $params = array()) {
    $params = http_build_query($params);
    return base_url($module) . ($params? '?'.$params : '');
}

/**
 * Set/Get nilai konfigurasi
 *
 * @param mixed $key
 * @param mixed $value
 * @return mixed
 */
function config($key, $value = null) {
    static $configs;

    // inisialisasi array config (hanya untuk pemanggilan pertama)
    if(!$configs) {
        $configs = array();
    }

    $args = func_get_args();

    // jika parameter yang diberikan hanya 1
    if (count($args) == 1) {
        if (is_array($key)) {
            // merge configs jika $key berupa array
            $configs = array_merge($configs, $key);
        } elseif (is_string($key)) {
            // kembalikan nilai konfigurasi jika key berupa string
            return array_key_exists($key, $configs)? $configs[$key] : null;
        } else {
            // selain itu error
            throw new InvalidArgumentException("Argumen '\$key' hanya dapat berupa array atau string.", 1);
        }
    } else {
        if (!is_string($key)) {
            throw new InvalidArgumentException("Argumen '\$key' hanya dapat berupa array atau string.", 1);
        }
        $configs[$key] = $value;
    }
}


/**
 * Mengambil inputan dari $_GET atau $_POST
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function input($key, $default = null) {
    $inputs = $_POST + $_GET;
    return isset($inputs[$key])? $inputs[$key] : $default;
}

/**
 * Redirect halaman
 *
 * @param string $url
 * @param array $params
 */
function redirect($url, array $params = []) {
    if (! empty($params)) {
        // resolve query string
        $expl = explode('?', $url, 2);
        $url_has_params = count($expl) > 1;
        if ($url_has_params) {
            parse_str($expl[1], $url_params);
            $params = array_merge($url_params, $params);
        }
        $url = $expl[0] . '?' . http_build_query($params);
    }

    header('Location: '.$url);
    exit;
}

/**
 * Mengambil path dari request
 * Contoh: http://aplikasinya.com/path/to/module?param=nilai => /path/to/page
 *
 * @return string
 */
function get_request_path() {
    if (array_key_exists('PATH_INFO', $_SERVER)) {
        $uri = $_SERVER['PATH_INFO'];
    } else {
        $file = ltrim($_SERVER['SCRIPT_NAME'],'/');
        $self = ltrim($_SERVER['PHP_SELF'], '/');
        $uri = preg_replace("#^".$file."#", '', $self);
    }

    return ltrim($uri, '/');
}

/**
 * Mengampilkan response berupa JSON
 *
 * @param array $data
 */
function response_json(array $data) {
    header('Content-type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Menampilkan error dan menghentikan aplikasi
 *
 * @param string|array $message
 * @param int $code HTTP status code (4xx/5xx)
 */
function abort($message, $code = 500) {
    http_response_code($code);
    if (is_array($message)) {
        // jika message berupa array, kirim json
        response_json($message);
    } else {
        // jika message berupa string, tampilkan halaman/pesan error
        $error_template = config('path.template').'/'.$code.'.php';
        if (file_exists($error_template)) {
            include($error_template);
        } else {
            echo $message;
        }
        exit;
    }
}

/**
 * Inisialisasi dan Mengambil koneksi database berdasarkan settingan pada konfigurasi
 * Dengan begini koneksi ke database akan otomatis dibuat saat module membutuhkannya
 *
 * @return PDO
 */
function db() {
    static $connection;
    // jika koneksi belum dibuat, buat dulu!
    if (!$connection) {
        // mengambil nilai konfigurasi untuk koneksi database
        $driver = config('database.driver') ?: 'mysql';
        $host = config('database.host') ?: 'localhost';
        $username = config('database.username') ?: 'root';
        $password = config('database.password');
        $dbname = config('database.name');

        // inisialisasi koneksi ke database
        $connection = new PDO("{$driver}:host={$host};dbname={$dbname}", $username, $password);
        // lempar exception jika terjadi error saat PDO menjalankan query
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // atur agar PDO secara default mengembalikan nilai berupa array associative saat melakukan fetch data
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    return $connection;
}

/**
 * Get/Set nilai session
 *
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function session($key, $value = null) {
    // jika session belum di start, start session!
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $args = func_get_args();

    // jika count parameter hanya 1,
    if (count($args) == 1) {
        // kembalikan nilai session (atau null jika tidak ada)
        return isset($_SESSION[$key])? $_SESSION[$key] : null;
    }
    else // jika parameter > 1
    {
        if (is_null($value)) {
            // jika value = null, unset session
            unset($_SESSION[$key]);
        } else {
            // selain itu, set nilai session
            $_SESSION[$key] = $value;
        }
    }
}
