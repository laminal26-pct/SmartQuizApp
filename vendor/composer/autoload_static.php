<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbfba4d533506007ef9b325f204a1db24
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/config',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbfba4d533506007ef9b325f204a1db24::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbfba4d533506007ef9b325f204a1db24::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
