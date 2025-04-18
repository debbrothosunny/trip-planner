<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdfb9692403dc298bd8eccc9b617885ce
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
        'C' => 
        array (
            'Core\\' => 5,
        ),
        'A' => 
        array (
            'App\\Models\\' => 11,
            'App\\Controllers\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Core',
        ),
        'App\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Models',
        ),
        'App\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Controllers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdfb9692403dc298bd8eccc9b617885ce::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdfb9692403dc298bd8eccc9b617885ce::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdfb9692403dc298bd8eccc9b617885ce::$classMap;

        }, null, ClassLoader::class);
    }
}
