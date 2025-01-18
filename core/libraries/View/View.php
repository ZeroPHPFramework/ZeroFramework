<?php
namespace Zero\Lib;

use Exception;


class View
{
    private static $sections = [];
    private static $currentSection;
    private static $layout;
    private static $config = [
        'cache_enabled' => false,
        'cache_path' => '/storage/cache/views',
        'cache_lifetime' => 3600,
        'debug' => false
    ];

    /**
     * Configure the view system
     *
     * @param array $config
     * @return void
     */
    public static function configure($config = [])
    {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * Get the cached file path for a view
     *
     * @param string $view
     * @return string
     */
    private static function getCacheFilePath($view)
    {
        $hash = md5($view);
        return rtrim(self::$config['cache_path'], '/') . "/views/cache/{$hash}.php";
    }

    /**
     * Check if a cached view is valid
     *
     * @param string $cachePath
     * @param string $viewPath
     * @return bool
     */
    private static function isCacheValid($cachePath, $viewPath)
    {
        if (!file_exists($cachePath)) {
            return false;
        }

        // Check cache lifetime
        if (time() - filemtime($cachePath) > self::$config['cache_lifetime']) {
            return false;
        }

        // Check if original view file has been modified
        if (filemtime($viewPath) > filemtime($cachePath)) {
            return false;
        }

        return true;
    }

    /**
     * Render a view template
     *
     * @param string $view
     * @param array $data
     * @return void
     * @throws Exception
     */
    public static function render($view, $data = [])
    {
        $viewFile = base("resources/views/{$view}.php");


        if (!file_exists($viewFile)) {
            throw new Exception("View file {$viewFile} not found.");
        }

        // Handle caching
        if (self::$config['cache_enabled']) {
            $cacheFile = self::getCacheFilePath($view);
            
            // Create cache directory if it doesn't exist
            if (!is_dir(dirname($cacheFile))) {
                mkdir(dirname($cacheFile), 0777, true);
            }

            // Use cached version if valid
            if (self::isCacheValid($cacheFile, $viewFile)) {
                if (self::$config['debug']) {
                    self::log("Using cached version of view: {$view}");
                }
                extract($data);
                include $cacheFile;
                return;
            }

            // Compile and cache the view
            $content = file_get_contents($viewFile);
            $compiled = self::processDirectives($content);
            file_put_contents($cacheFile, $compiled);
            
            if (self::$config['debug']) {
                self::log("Cached new version of view: {$view}");
            }
        }

        // Extract data to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        include $viewFile;

        // Get the buffered content
        $content = ob_get_clean();
        
        // If a layout is defined, include the layout
        if (self::$layout) {
            $layoutFile = base("resources/views/" . self::$layout . ".php");
            if (!file_exists($layoutFile)) {
                throw new Exception("Layout file {$layoutFile} not found.");
            }
            ob_start();
            include $layoutFile;
            $content = ob_get_clean();
        }

        // Process Laravel-like directives
        $content = self::processDirectives($content);

        // Output the final processed content
        echo eval('?>' . $content);
    }

    /**
     * Start a section
     *
     * @param string $section
     * @return void
     */
    public static function startSection($section)
    {
        self::$currentSection = $section;
        ob_start();
    }

    /**
     * End the current section
     *
     * @return void
     */
    public static function endSection()
    {
        self::$sections[self::$currentSection] = self::processDirectives(ob_get_clean());
        self::$currentSection = null;
    }

    /**
     * Yield a section's content
     *
     * @param string $section
     * @return string
     */
    public static function yieldSection($section)
    {
        return self::$sections[$section] ?? '';
    }

    /**
     * Define the layout
     *
     * @param string $layout
     * @return void
     */
    public static function layout($layout)
    {
        self::$layout = $layout;
    }

    /**
     * Process Laravel-like directives in the view content
     *
     * @param string $content
     * @return string
     */
    private static function processDirectives($content)
    {
        // Handle @foreach directive
        $content = preg_replace_callback('/@foreach\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php foreach({$matches[1]}): ?>";
        }, $content);
        $content = str_replace('@endforeach', '<?php endforeach; ?>', $content);

        // Handle @if directive
        $content = preg_replace_callback('/@if\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php if({$matches[1]}): ?>";
        }, $content);
        $content = str_replace('@endif', '<?php endif; ?>', $content);

        // Handle @elseif directive
        $content = preg_replace_callback('/@elseif\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php elseif({$matches[1]}): ?>";
        }, $content);

        // Handle @else directive
        $content = str_replace('@else', '<?php else: ?>', $content);

        // Handle {{{ variable }}} for raw output
        $content = preg_replace_callback('/{{{(.*?)}}}/', function ($matches) {
            return "<?php echo {$matches[1]}; ?>";
        }, $content);

        // Handle {{ variable }} for escaped output
        $content = preg_replace_callback('/{{\s*(.+?)\s*}}/', function ($matches) {
            return "<?php echo htmlspecialchars({$matches[1]}, ENT_QUOTES, 'UTF-8'); ?>";
        }, $content);

        // Handle @include directive
        $content = preg_replace_callback('/@include\s*\((.*?)\)\s*/', function ($matches) {
            $includePath = eval('return ' . $matches[1] . ';');
            $includePath = base("/views/" . $includePath);
            if (file_exists($includePath)) {
                ob_start();
                include $includePath;
                return ob_get_clean();
            }
            throw new Exception("Included file not found: $includePath");
        }, $content);

        // Handle @yield directive
        $content = preg_replace_callback('/@yield\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php echo View::yieldSection({$matches[1]}); ?>";
        }, $content);

        // Handle @layout directive
        $content = preg_replace_callback('/@layout\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php View::layout({$matches[1]}); ?>";
        }, $content);

        // Handle @section directive
        $content = preg_replace_callback('/@section\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php View::startSection({$matches[1]}); ?>";
        }, $content);

        // Handle @endsection directive
        $content = str_replace('@endsection', '<?php View::endSection(); ?>', $content);

        // Handle @dd directive
        $content = preg_replace_callback('/@dd\s*\((.*?)\)\s*/', function ($matches) {
            return "<?php dd({$matches[1]}); ?>";
        }, $content);

        return $content;
    }

    /**
     * Clear the entire view cache
     *
     * @return void
     */
    public static function clearCache()
    {
        if (!self::$config['cache_enabled'] || !self::$config['cache_path']) {
            return;
        }

        $cacheDir = rtrim(self::$config['cache_path'], '/') . '/views/cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Include
     */
    public static function include($view) {
        $viewFile = base("resources/views/{$view}.php");
        if (!file_exists($viewFile)) {
            throw new Exception("View file {$viewFile} not found.");
        }
        include $viewFile;
    }

    /**
     * Clear cache for a specific view
     *
     * @param string $view
     * @return void
     */
    public static function clearViewCache($view)
    {
        if (!self::$config['cache_enabled'] || !self::$config['cache_path']) {
            return;
        }

        $cacheFile = self::getCacheFilePath($view);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            if (self::$config['debug']) {
                self::log("Cleared cache for view: {$view}");
            }
        }
    }

    /**
     * Log debug messages if debug mode is enabled
     *
     * @param string $message
     * @return void
     */
    private static function log($message)
    {
        if (self::$config['debug']) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$message}\n";
            $logFile = rtrim(self::$config['cache_path'], '/') . '/views/cache/view.log';
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
}