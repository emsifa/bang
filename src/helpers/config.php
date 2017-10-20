<?php

/**
 * Get/set configuration(s)
 *
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function config($key, $value = null) {
    static $configs;

    if( ! $configs ) {
        $configs = array();
    }

    $args = func_get_args();

    if ( count($args) == 1 ) {
        if ( is_array($key) ) {
            // merge configs jika $key berupa array
            $configs = array_merge($configs, $key);
        } elseif ( is_string($key) ) {
            // kembalikan nilai konfigurasi
            return array_key_exists($key, $configs)? $configs[$key] : null;
        } else {
            throw new \Exception("Argumen '\$key' hanya dapat berupa array atau string.", 1);
        }
    } else {
        if ( !is_string($key) ) {
            throw new \Exception("Argumen '\$key' hanya dapat berupa array atau string.", 1);
        }

        $configs[$key] = $value;
    }
}