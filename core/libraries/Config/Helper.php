<?php

if(!function_exists('config')){
    function config($key){
        $key = explode('.', $key);
        $file = $key[0];
        array_shift($key);
        $config = require_once(base('/config/' . $file . '.php'));
        foreach($key as $k){
            $config = $config[$k];
        }

        return $config;
        
    }
}