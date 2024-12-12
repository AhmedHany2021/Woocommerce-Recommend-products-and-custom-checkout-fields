<?php

namespace CUSTOMWOO\Includes;
class autoload
{
    public static function fire()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    private static function autoload ($class)
    {
        $prefix = 'CUSTOMWOO\\Includes\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $relative_class = str_replace('\\', '/', $relative_class);
        $base_dir = __DIR__ . '/';
        $file = $base_dir . $relative_class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}