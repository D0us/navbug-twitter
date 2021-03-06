<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd56475d5225cfc84f388d7c40ddafac7
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Abraham\\TwitterOAuth\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Abraham\\TwitterOAuth\\' => 
        array (
            0 => __DIR__ . '/..' . '/abraham/twitteroauth/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd56475d5225cfc84f388d7c40ddafac7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd56475d5225cfc84f388d7c40ddafac7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
