<?php

// Initialize the kernel if it isn't set already
$kernel = isset($kernel) ? $kernel : require_once(core_path('kernel.php'));
// Get the aliases from the kernel configuration
$aliases = $kernel['aliases'];

/**
 * Function to get the path of a library file.
 *
 * @param string $file The name of the library file
 * @return string The full path to the library file
 */
function getLibPath(string $file): string {
    // Check the path for the library file in the standard location
    $filePath = core_path("libraries/{$file}.php");
    if(file_exists($filePath)) {
        return $filePath;
    }

    // If not found, check if it's in a subfolder with the same name as the file
    $filePath = core_path("libraries/{$file}/{$file}.php");

    return $filePath;
}

/**
 * Autoloader function that loads classes dynamically.
 */
spl_autoload_register(function ($className) use ($aliases) {
    
    // Store the original class name for alias handling
    $originalClassName = $className;
    // Flag to check if the alias is being used
    $canAliases        = false;


    // If the class has an alias, update the class name to the alias
    if(array_key_exists($className, $aliases)) {
        $canAliases = true;
        $className = $aliases[$className];
    }

    // Handle classes in the Zero\Lib namespace
    if(strpos($className, 'Zero\Lib') !== false) {
        // Remove the namespace prefix
        $className = str_replace('Zero\Lib\\', '', $className);
        // Get the full path of the class
        $class_path = lib_path("$className/$className.php");

        // If the class file exists, require it
        if (file_exists($class_path)) {
            require_once $class_path;
            // If the class has an alias, create an alias for the original class name
            if($canAliases) {
                if(!class_exists($originalClassName)) {
                    class_alias($aliases[$originalClassName], $originalClassName);
                }
            }
            return;
        } else {
            // If the class file is not found, throw an exception
            throw new Exception("Class $className not found in $class_path");
        }
    }

    // Handle classes in the App\Controllers namespace
    if(strpos($className, 'App\Controllers') !== false) {
        // Remove the namespace prefix
        $className = str_replace('App\Controllers\\', '', $className);
        // Get the full path of the controller file
        $class_path = app_path("controllers/$className.php");
        // If the controller file exists, require it
        if (file_exists($class_path)) {
            require_once $class_path;
            return;
        }
    }

    // Handle classes in the Drivers namespace
    if(strpos($className, 'Drivers') !== false) {
        // Remove the namespace prefix and the 'Driver' suffix
        $className = str_replace('Zero\Drivers\\', '', $className);
        $className = str_replace('Drivers\\', '', $className);
        $fileName = str_replace('Driver', '', $className);
        
        // Get the full path of the driver file
        $class_path = core_path("drivers/$fileName.php");
        // If the driver file exists, require it
        if (file_exists($class_path)) {
            require_once $class_path;
            return;
        }
    }

});
