<?php

$aliases = (require_once(core_path('kernel.php')))['aliases'];


spl_autoload_register(function ($className) use ($aliases) {
    
    $originalClassName = $className;
    $canAliases        = false;
    

    if(array_key_exists($className, $aliases)) {
        $canAliases = true;
        $className = $aliases[$className];
    }


    if(strpos($className, 'Zero\Lib') !== false) {
        $className = str_replace('Zero\Lib\\', '', $className);
        $class_path = lib_path("$className/$className.php");
        
        if (file_exists($class_path)) {
            require_once $class_path;
            // check is have stub
            if(file_exists(lib_path("$className/Stub.php"))) {
                require_once lib_path("$className/Stub.php");
            }
            if($canAliases) {
                class_alias($aliases[$originalClassName], $originalClassName);
            }
            return;
        } else {
            throw new Exception("Class $className not found in $class_path");
        }
    }

    // App\Controllers
    if(strpos($className, 'App\Controllers') !== false) {
        $className = str_replace('App\Controllers\\', '', $className);
        $class_path = app_path("controllers/$className.php");
        if (file_exists($class_path)) {
            require_once $class_path;
            return;
        }
    }


});
