<?php

if(!function_exists('config')){
    $configs = [];

    function config($key){
        global $configs;
        $key = explode('.', $key);
        $file = $key[0];
        array_shift($key);
        $config = isset($configs[$file]) ? $configs[$file] : require_once(base('/config/' . $file . '.php'));

        $configs[$file] = $config;

        if($configs[$file]){
            $config = $configs[$file];
        } else {
            $configs[$file] = $config;
        }

        foreach($key as $k){
            $config = $config[$k];
        }

        return $config;
        
    }
}