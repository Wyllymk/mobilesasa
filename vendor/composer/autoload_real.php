<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit40d105e76efb9358c6b437b7d71df89e
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit40d105e76efb9358c6b437b7d71df89e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit40d105e76efb9358c6b437b7d71df89e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit40d105e76efb9358c6b437b7d71df89e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}